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

        return view('attendance.index', compact('currentDate', 'currentTime', 'status'));
    }

    public function start()
    {
        $user = Auth::user();
        $today = Carbon::today()->format('Y-m-d');

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
        $monthParam = $request->input('month', Carbon::now()->format('Y-m'));
        $currentMonthStr = Carbon::now()->format('Y-m');

        if ($monthParam > $currentMonthStr) {
            abort(403, '未来の月は表示できません。');
        }

        $targetCarbon = Carbon::parse($monthParam . '-01');
        $currentMonth = $targetCarbon->format('Y-m');
        $prevMonth = $targetCarbon->copy()->subMonth()->format('Y-m');
        $nextMonth = $targetCarbon->copy()->addMonth()->format('Y-m');

        $showNextButton = $nextMonth <= $currentMonthStr;

        // 1. データベースから当月のデータを一括取得（日付をキーにした連想配列にする）
        $dbAttendances = Attendance::with('rests')
            ->where('user_id', Auth::id())
            ->whereYear('date', $targetCarbon->year)
            ->whereMonth('date', $targetCarbon->month)
            ->get()
            ->keyBy('date'); // '2026-05-01' のような日付文字列をキーにする

        // 2. その月の「1日」から「末日」までの全日付のカードを格納する配列を用意
        $allDaysAttendances = [];
        $daysInMonth = $targetCarbon->daysInMonth; // その月が何日あるか（30日か31日か）

        for ($day = 1; $day <= $daysInMonth; $day++) {
            // ループ中の具体的な日付を作成（例：2026-05-01）
            $dateStr = $targetCarbon->copy()->day($day)->format('Y-m-d');

            // 曜日付きの表示用日付を作成（例：05/01(金)）
            $displayDate = $targetCarbon->copy()->day($day)->isoFormat('MM/DD(ddd)');

            // 3. データベースにその日のデータがあるかチェック
            if ($dbAttendances->has($dateStr)) {
                // データがある場合は、既存のデータを使う
                $attendance = $dbAttendances->get($dateStr);

                // --- ここから時間の計算ロジック（元のコードをそのまま流用） ---
                $punchIn = Carbon::parse($attendance->punch_in)->startOfMinute();
                if ($attendance->punch_out) {
                    $punchOut = Carbon::parse($attendance->punch_out)->startOfMinute();
                    $stayMinutes = $punchIn->diffInMinutes($punchOut);
                } else {
                    $stayMinutes = 0;
                }

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
                // --- ここまで時間の計算ロジック ---

                // 表示用の日付をセット
                $attendance->display_date = $displayDate;
                $allDaysAttendances[] = $attendance;
            } else {
                // 4. データがない日（土日や未打刻日）は、空白のダミーオブジェクトを作る
                $allDaysAttendances[] = (object)[
                    'id'            => null, // 詳細画面へ遷移させない、または制御するため
                    'display_date'  => $displayDate,
                    'punch_in'      => null,
                    'punch_out'     => null,
                    'display_rest'  => '', // ✨ 空文字（''）に変更して00:00を消します
                    'display_total' => '', // ✨ 空文字（''）に変更して00:00を消します
                ];
            }
        }

        // 最後に、全日付が詰まった配列をビューに渡す
        return view('attendance.list', [
            'attendances'     => $allDaysAttendances,
            'currentMonth'    => $currentMonth,
            'prevMonth'       => $prevMonth,
            'nextMonth'       => $nextMonth,
            'showNextButton'  => $showNextButton
        ]);
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
