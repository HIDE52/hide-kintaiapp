<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        return view('attendance.index', compact('status'));
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
