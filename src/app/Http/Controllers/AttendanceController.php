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
                    ->latest()
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

    public function breakStart()
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

    public function breakEnd()
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
}
