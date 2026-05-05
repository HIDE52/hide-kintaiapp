<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        return view('user.attendance.index', compact('status'));
    }

    public function list()
    {
        return view('user.attendance.list');
    }

    public function show($id)
    {
        return view('user.attendance.detail');
    }
}
