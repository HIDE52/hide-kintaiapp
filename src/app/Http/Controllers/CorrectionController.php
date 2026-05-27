<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\CorrectionAttendance;
use App\Models\CorrectionRest;
use App\Http\Requests\AttendanceUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CorrectionController extends Controller
{
    public function index(Request $request)
    {
        $currentTab = $request->query('tab', 'waiting');

        if (!in_array($currentTab, ['waiting', 'approved'])) {
            $currentTab = 'waiting';
        }

        $query = CorrectionAttendance::where('user_id', Auth::id())
            ->with(['attendance']);

        if ($currentTab === 'waiting') {
            $query->where('status', 0);
        } else {
            $query->where('status', 1);
        }

        $requests = $query->orderBy('created_at', 'asc')->get();

        return view('correction.list', compact('currentTab', 'requests'));
    }

    public function store(AttendanceUpdateRequest $request, $id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance || $attendance->user_id !== Auth::id()) {
            return redirect()->route('attendance.list');
        }

        $latestCorrection = CorrectionAttendance::where('attendance_id', $id)->latest()->first();
        if ($latestCorrection && $latestCorrection->status === 0) {
            return redirect()->back()->withErrors(['error' => '承認待ちのため修正はできません。']);
        }

        DB::transaction(function () use ($request, $attendance) {

            $punchIn  = $request->input('punch_in');
            $punchOut = $request->input('punch_out');

            if (!is_null($punchIn)) {
                $punchIn = str_replace('：', ':', $punchIn);
            }
            if (!is_null($punchOut)) {
                $punchOut = str_replace('：', ':', $punchOut);
            }

            $correctionAttendance = CorrectionAttendance::create([
                'attendance_id'       => $attendance->id,
                'user_id'             => Auth::id(),
                'requested_punch_in'  => $punchIn,
                'requested_punch_out' => $punchOut,
                'status'              => 0,
                'remark'              => $request->input('remark'),
            ]);

            $restData = $request->input('rest_id', []);

            foreach ($restData as $index => $restItem) {
                $breakIn  = $restItem['break_in'] ?? null;
                $breakOut = $restItem['rest_id'][$index]['break_out'] ?? $restItem['break_out'] ?? null;

                if (is_null($breakIn) && is_null($breakOut)) {
                    continue;
                }

                if (!is_null($breakIn)) {
                    $breakIn = str_replace('：', ':', $breakIn);
                }
                if (!is_null($breakOut)) {
                    $breakOut = str_replace('：', ':', $breakOut);
                }

                CorrectionRest::create([
                    'attendance_correction_id' => $correctionAttendance->id,
                    'requested_break_in'       => $breakIn,
                    'requested_break_out'      => $breakOut,
                ]);
            }
        });

        return redirect()->back()->with('session_success', '修正申請を提出しました。');
    }
}
