<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');

        $currentDate = Carbon::now()->isoFormat('YYYY年MM月DD日(ddd)');
        $currentTime = Carbon::now()->format('H:i');

        $unfinishedAttendance = Attendance::where('user_id', $user->id)
            ->where('date', '<', $today)
            ->whereNull('punch_out')
            ->first();

        $hasForgotEndStamp = false;
        $yesterdayId = null;

        if ($unfinishedAttendance) {
            $hasForgotEndStamp = true;
            $yesterdayId = $unfinishedAttendance->id;
        }

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $status = '勤務外';

        if ($unfinishedAttendance) {
            $status = '勤務外';
        } elseif ($attendance) {
            if ($attendance->punch_out) {
                $status = '退勤済';
            } else {
                $latestRest = Rest::where('attendance_id', $attendance->id)
                    ->latest('id')
                    ->first();

                if ($latestRest && is_null($latestRest->break_out)) {
                    $status = '休憩中';
                } else {
                    $status = '出勤中';
                }
            }
        }

        return view('attendance.index', compact(
            'currentDate',
            'currentTime',
            'status',
            'hasForgotEndStamp',
            'yesterdayId'
        ));
    }

    public function start()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');

        $unfinishedAttendance = Attendance::where('user_id', $user->id)
            ->where('date', '<', $today)
            ->whereNull('punch_out')
            ->exists();

        if ($unfinishedAttendance) {
            return redirect()->back();
        }

        $exists = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->exists();

        if ($exists) {
            return redirect()->back();
        }

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'punch_in' => Carbon::now()->format('H:i:s'),
        ]);

        return redirect()->back();
    }

    public function restStart()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance || $attendance->punch_out) {
            return redirect()->back();
        }

        Rest::create([
            'attendance_id' => $attendance->id,
            'break_in' => Carbon::now()->format('H:i:s'),
        ]);

        return redirect()->back();
    }

    public function restEnd()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return redirect()->back();
        }

        $latestRest = Rest::where('attendance_id', $attendance->id)
            ->whereNull('break_out')
            ->latest()
            ->first();

        if ($latestRest) {
            $latestRest->update([
                'break_out' => Carbon::now()->format('H:i:s'),
            ]);
        }

        return redirect()->back();
    }

    public function end()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance || $attendance->punch_out) {
            return redirect()->back();
        }

        if ($attendance->date !== $today) {
            return redirect()->back();
        }

        $hasActiveRest = Rest::where('attendance_id', $attendance->id)
            ->whereNull('break_out')
            ->exists();

        if ($hasActiveRest) {
            return redirect()->back();
        }

        $attendance->update([
            'punch_out' => Carbon::now()->format('H:i:s'),
        ]);

        return redirect()->back()->with('session_success', 'お疲れ様でした。');
    }

    public function list(Request $request)
    {
        $user = Auth::user();
        $currentMonthStr = Carbon::now()->format('Y-m');
        $monthParam = $request->query('month', $currentMonthStr);

        if (!preg_match('/^[0-9]{4}-[0-9]{2}$/', $monthParam)) {
            abort(404);
        }

        if ($monthParam > $currentMonthStr) {
            abort(403, '未来の月は表示できません。');
        }

        $targetCarbon = Carbon::parse($monthParam . '-01');

        $dbAttendances = $user->attendances()
            ->whereYear('date', $targetCarbon->year)
            ->whereMonth('date', $targetCarbon->month)
            ->orderBy('date', 'asc')
            ->get();

        foreach ($dbAttendances as $attendance) {
            if ($attendance->punch_out) {
                $attendance->display_rest = $attendance->total_rest_time;
                $attendance->display_total = $attendance->total_work_time;
            } else {
                $attendance->display_rest = '';
                $attendance->display_total = '';
            }
        }

        $daysInMonth = $targetCarbon->daysInMonth;
        $attendances = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $loopDate = $targetCarbon->copy()->day($day);
            $dateStr = $loopDate->format('Y-m-d');

            $weeks = ['日', '月', '火', '水', '木', '金', '土'];
            $weekStr = $weeks[$loopDate->dayOfWeek];
            $displayDate = $loopDate->format('m/d') . '(' . $weekStr . ')';

            $matchedAttendance = $dbAttendances->firstWhere('date', $dateStr);

            if ($matchedAttendance) {
                $matchedAttendance->display_date = $displayDate;
                $attendances[] = $matchedAttendance;
            } else {
                $emptyAttendance = new \stdClass();
                $emptyAttendance->id = null;
                $emptyAttendance->date = $dateStr;
                $emptyAttendance->display_date = $displayDate;
                $emptyAttendance->punch_in = null;
                $emptyAttendance->punch_out = null;
                $emptyAttendance->display_rest = '';
                $emptyAttendance->display_total = '';

                $attendances[] = $emptyAttendance;
            }
        }

        $currentMonth = $targetCarbon->format('Y-m');
        $prevMonth = $targetCarbon->copy()->subMonth()->format('Y-m');
        $nextMonth = $targetCarbon->copy()->addMonth()->format('Y-m');
        $showNextButton = ($nextMonth > $currentMonthStr) ? false : true;
        $displayMonth = $targetCarbon->format('Y/m');

        return view('attendance.list', compact(
            'attendances',
            'currentMonth',
            'prevMonth',
            'nextMonth',
            'showNextButton',
            'displayMonth'
        ));
    }

    public function show($id)
    {
        $attendance = Attendance::with([
            'rests',
            'correctionAttendances.correctionRests'
        ])->find($id);

        if (!$attendance || $attendance->user_id !== Auth::id()) {
            return redirect()->route('attendance.list');
        }

        $latestCorrection = $attendance->correctionAttendances
            ->where('attendance_id', $attendance->id)
            ->sortByDesc('created_at')
            ->first();

        $isPending = $latestCorrection && $latestCorrection->status === 0;

        if ($isPending) {
            $attendance->punch_in = $latestCorrection->requested_punch_in;
            $attendance->punch_out = $latestCorrection->requested_punch_out;
            $attendance->note = $latestCorrection->remark;

            $formattedRests = [];
            foreach ($latestCorrection->correctionRests as $cRest) {
                $formattedRests[] = (object)[
                    'break_in' => $cRest->requested_break_in,
                    'break_out' => $cRest->requested_break_out
                ];
            }
            $attendance->setRelation('rests', collect($formattedRests));
        }

        $currentDate = Carbon::parse($attendance->date)->isoFormat('YYYY年MM月DD日(ddd)');

        return view('attendance.detail', compact('attendance', 'currentDate', 'isPending'));
    }
}
