<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = User::where('role', 2)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.staff.list', compact('staffs'));
    }

    public function showStaff(Request $request, $id)
    {
        $staff = User::where('role', 2)->findOrFail($id);
        $currentMonthStr = Carbon::now()->format('Y-m');

        $monthParam = $request->query('tab', $currentMonthStr);

        if (!preg_match('/^[0-9]{4}-[0-9]{2}$/', $monthParam)) {
            abort(404);
        }

        if ($monthParam > $currentMonthStr) {
            abort(403, '未来の月は表示できません。');
        }

        $targetCarbon = Carbon::parse($monthParam . '-01');

        $dbAttendances = $staff->attendances()
            ->whereYear('date', $targetCarbon->year)
            ->whereMonth('date', $targetCarbon->month)
            ->orderBy('date', 'asc')
            ->get();

        foreach ($dbAttendances as $attendance) {
            $punchIn = Carbon::parse($attendance->punch_in)->startOfMinute();

            if ($attendance->punch_out) {
                $punchOut = Carbon::parse($attendance->punch_out)->startOfMinute();
                $stayMinutes = $punchIn->diffInMinutes($punchOut);

                $totalRestMinutes = 0;
                foreach ($attendance->rests as $rest) {
                    $breakIn = Carbon::parse($rest->break_in)->startOfMinute();
                    if ($rest->break_out) {
                        $breakOut = Carbon::parse($rest->break_out)->startOfMinute();
                        $totalRestMinutes += $breakIn->diffInMinutes($breakOut);
                    }
                }

                $totalWorkingMinutes = $stayMinutes - $totalRestMinutes;

                if ($totalWorkingMinutes > 0) {
                    $hours = floor($totalWorkingMinutes / 60);
                    $mins = $totalWorkingMinutes % 60;
                    $attendance->display_total = sprintf('%02d:%02d', $hours, $mins);
                } else {
                    $attendance->display_total = '00:00';
                }

                $restHours = floor($totalRestMinutes / 60);
                $restMins = $totalRestMinutes % 60;
                $attendance->display_rest = sprintf('%02d:%02d', $restHours, $restMins);
            } else {
                $attendance->display_total = '';
                $attendance->display_rest = '';
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

        return view('admin.staff.show', compact(
            'staff',
            'attendances',
            'currentMonth',
            'prevMonth',
            'nextMonth',
            'showNextButton'
        ));
    }

    public function exportCsv(Request $request, $id)
    {
        $staff = User::where('role', 2)->findOrFail($id);
        $currentMonthStr = Carbon::now()->format('Y-m');

        $monthParam = $request->query('tab', $currentMonthStr);

        if (!preg_match('/^[0-9]{4}-[0-9]{2}$/', $monthParam)) {
            abort(404);
        }

        if ($monthParam > $currentMonthStr) {
            abort(403, '未来の月のデータは出力できません。');
        }

        $targetCarbon = Carbon::parse($monthParam . '-01');

        $attendances = $staff->attendances()
            ->whereYear('date', $targetCarbon->year)
            ->whereMonth('date', $targetCarbon->month)
            ->orderBy('date', 'asc')
            ->get();

        $cleanName = str_replace([' ', ' '], '', $staff->name);
        $fileName = "{$cleanName}_{$monthParam}_勤怠.csv";

        $response = new StreamedResponse(function () use ($attendances) {
            $stream = fopen('php://output', 'w');

            $headers = ['日付', '出勤', '退勤', '休憩時間', '合計実働時間'];
            mb_convert_variables('SJIS-win', 'UTF-8', $headers);
            fputcsv($stream, $headers);

            foreach ($attendances as $attendance) {
                $carbonDate = Carbon::parse($attendance->date);
                $weeks = ['日', '月', '火', '水', '木', '金', '土'];
                $weekStr = $weeks[$carbonDate->dayOfWeek];

                $dateStr = $carbonDate->format('m/d') . '(' . $weekStr . ')';
                $punchInStr = $attendance->punch_in ? Carbon::parse($attendance->punch_in)->format('H:i') : '';
                $punchOutStr = $attendance->punch_out ? Carbon::parse($attendance->punch_out)->format('H:i') : '';
                $restTimeStr = ($attendance->punch_in && $attendance->punch_out) ? $attendance->total_rest_time : '';
                $workTimeStr = $attendance->total_work_time;

                $row = [
                    $dateStr,
                    $punchInStr,
                    $punchOutStr,
                    $restTimeStr,
                    $workTimeStr
                ];

                mb_convert_variables('SJIS-win', 'UTF-8', $row);
                fputcsv($stream, $row);
            }

            fclose($stream);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=SJIS-win');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

        return $response;
    }
}
