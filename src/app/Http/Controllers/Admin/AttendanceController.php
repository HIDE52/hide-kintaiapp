<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use App\Http\Requests\AttendanceUpdateRequest;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $dateParam = $request->query('date', Carbon::today()->toDateString());

        if (!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dateParam)) {
            abort(404);
        }

        try {
            $targetDate = Carbon::parse($dateParam);
        } catch (\Exception $e) {
            abort(404);
        }

        if ($targetDate->isFuture()) {
            abort(403, '未来の日付は表示できません。');
        }

        $prevDate = $targetDate->copy()->subDay()->toDateString();
        $nextDate = $targetDate->copy()->addDay()->toDateString();

        $showNextButton = true;
        if ($nextDate > Carbon::today()->toDateString()) {
            $showNextButton = false;
        }

        $attendances = Attendance::with('user')
            ->where('date', $targetDate->toDateString())
            ->whereHas('user', function ($query) {
                $query->where('role', 2);
            })
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->orderBy('users.created_at', 'asc')
            ->select('attendances.*')
            ->get();

        $displayDate = $targetDate->format('Y/m/d');

        return view('admin.attendance.list', compact(
            'attendances',
            'displayDate',
            'prevDate',
            'nextDate',
            'showNextButton'
        ));
    }

    public function show($id)
    {
        $attendance = Attendance::with(['rests', 'correctionAttendances'])->findOrFail($id);
        $isPending = $attendance->correctionAttendances->where('status', 0)->isNotEmpty();

        return view('admin.attendance.detail', compact('attendance', 'isPending'));
    }

    public function update(AttendanceUpdateRequest $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'punch_in'  => $request->punch_in,
            'punch_out' => $request->punch_out,
        ]);

        $attendance->rests()->delete();

        if ($request->has('rest_id')) {
            foreach ($request->rest_id as $restData) {
                if (!empty($restData['break_in']) && !empty($restData['break_out'])) {
                    $attendance->rests()->create([
                        'break_in'  => $restData['break_in'],
                        'break_out' => $restData['break_out'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.attendance.index')->with('success', '勤怠データを直接修正しました。');
    }

    public function showStaffAttendance($id)
    {
        return view('admin.staff.show');
    }
}
