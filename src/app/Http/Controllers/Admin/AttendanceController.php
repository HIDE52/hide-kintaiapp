<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('admin.attendance.list'); //
    }

    public function show($id)
    {
        return view('admin.attendance.detail'); //
    }

    public function showStaffAttendance($id)
    {
        return view('admin.staff.show'); //
    }
}
