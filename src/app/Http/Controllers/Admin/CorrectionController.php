<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CorrectionAttendance;

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

    public function showApprove($id)
    {
        return view('admin.correction.approve');
    }

    public function approve($id)
    {
        // 承認ロジック用（次回実装）
    }
}
