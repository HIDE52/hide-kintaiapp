<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CorrectionAttendance;
use Illuminate\Support\Facades\DB;

class CorrectionController extends Controller
{
    public function index(Request $request)
    {
        $currentTab = $request->query('tab', 'waiting');

        $query = CorrectionAttendance::with(['user', 'attendance']);

        if ($currentTab === 'waiting') {
            $requests = $query->where('status', 0)
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            $requests = $query->where('status', 1)
                ->get();
        }

        return view('admin.correction.list', compact('currentTab', 'requests'));
    }

    public function showApprove($attendance_correct_request_id)
    {
        $requestItem = CorrectionAttendance::with(['user', 'attendance', 'correctionRests'])
            ->findOrFail($attendance_correct_request_id);

        return view('admin.correction.approve', compact('requestItem'));
    }

    public function approve(Request $request, $attendance_correct_request_id)
    {
        $requestItem = CorrectionAttendance::with(['attendance', 'correctionRests'])
            ->findOrFail($attendance_correct_request_id);

        DB::transaction(function () use ($requestItem) {
            $attendance = $requestItem->attendance;
            $attendance->update([
                'punch_in' => $requestItem->requested_punch_in,
                'punch_out' => $requestItem->requested_punch_out,
            ]);

            foreach ($requestItem->correctionRests as $index => $correctionRest) {
                $actualRest = $attendance->rests->get($index);

                if ($actualRest) {
                    $actualRest->update([
                        'break_in' => $correctionRest->requested_break_in,
                        'break_out' => $correctionRest->requested_break_out,
                    ]);
                } else {
                    $attendance->rests()->create([
                        'break_in' => $correctionRest->requested_break_in,
                        'break_out' => $correctionRest->requested_break_out,
                    ]);
                }
            }

            $requestItem->update([
                'status' => 1
            ]);
        });

        return redirect()->route('admin.request.index');
    }
}
