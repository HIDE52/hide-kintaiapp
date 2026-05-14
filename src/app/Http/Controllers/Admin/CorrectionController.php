<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CorrectionController extends Controller
{
    public function index()
    {
        return view('admin.correction.list'); //
    }

    public function showApprove($id)
    {
        return view('admin.correction.approve'); //
    }
}
