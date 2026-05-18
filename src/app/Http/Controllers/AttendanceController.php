<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today()->format('Y-m-d');
        $user = Auth::user();
        $attendance = $user->attendances()->where('date', $today)->first();

        $status = '勤務外';

        if ($attendance) {
            if (!is_null($attendance->punch_out)) {
                $status = '退勤済';
            } else {
                $latestRest = $attendance->rests()->latest()->first();
                if ($latestRest && is_null($latestRest->break_out)) {
                    $status = '休憩中';
                } else {
                    $status = '出勤中';
                }
            }
        }

        return view('attendance.index', compact('status'));
    }

    public function start()
    {
        $today = Carbon::today()->format('Y-m-d');
        $user = Auth::user();

        $exists = $user->attendances()->where('date', $today)->exists();
        if ($exists) {
            return redirect()->back();
        }

        $user->attendances()->create([
            'date' => $today,
            'punch_in' => Carbon::now()->format('H:i:s'),
        ]);

        return redirect('/attendance');
    }

    public function end()
    {
        $today = Carbon::today()->format('Y-m-d');
        $user = Auth::user();
        $attendance = $user->attendances()->where('date', $today)->first();

        if ($attendance && is_null($attendance->punch_out)) {
            $attendance->update([
                'punch_out' => Carbon::now()->format('H:i:s'),
            ]);
            return redirect('/attendance')->with('success', 'お疲れ様でした。');
        }

        return redirect('/attendance');
    }

    public function restStart()
    {
        $today = Carbon::today()->format('Y-m-d');
        $user = Auth::user();
        $attendance = $user->attendances()->where('date', $today)->first();

        if ($attendance && is_null($attendance->punch_out)) {
            $latestRest = $attendance->rests()->latest()->first();
            if (!$latestRest || !is_null($latestRest->break_out)) {
                $attendance->rests()->create([
                    'break_in' => Carbon::now()->format('H:i:s'),
                ]);
            }
        }

        return redirect('/attendance');
    }

    public function restEnd()
    {
        $today = Carbon::today()->format('Y-m-d');
        $user = Auth::user();
        $attendance = $user->attendances()->where('date', $today)->first();

        if ($attendance) {
            $latestRest = $attendance->rests()->whereNull('break_out')->latest()->first();
            if ($latestRest) {
                $latestRest->update([
                    'break_out' => Carbon::now()->format('H:i:s'),
                ]);
            }
        }

        return redirect('/attendance');
    }

    public function list()
    {
        return view('attendance.list');
    }

    public function show($id)
    {
        return view('attendance.detail');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
