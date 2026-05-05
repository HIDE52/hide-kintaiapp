<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CorrectionController extends Controller
{
    public function index()
    {
        return view('admin.request.list'); //
    }

    public function showApprove($id)
    {
        return view('admin.request.approve'); //
    }
}
