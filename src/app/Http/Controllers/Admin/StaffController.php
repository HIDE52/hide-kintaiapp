<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

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
        $user = User::where('role', 2)->findOrFail($id);
        $currentMonth = Carbon::now()->startOfMonth();
        $dateParam = $request->query('tab');

        if ($dateParam) {
            $targetDate = Carbon::parse($dateParam)->startOfMonth();
        } else {
            $targetDate = $currentMonth->copy();
        }

        if ($targetDate->isAfter($currentMonth)) {
            abort(403, '未来の勤怠データは閲覧できません。');
        }

        $attendances = $user->attendances()
            ->whereYear('date', $targetDate->year)
            ->whereMonth('date', $targetDate->month)
            ->orderBy('date', 'asc')
            ->get();

        $prevMonth = $targetDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $targetDate->copy()->addMonth()->format('Y-m');
        $isCurrentMonth = $targetDate->equalTo($currentMonth);

        return view('admin.staff.show', compact(
            'user',
            'attendances',
            'targetDate',
            'prevMonth',
            'nextMonth',
            'isCurrentMonth'
        ));
    }
}
