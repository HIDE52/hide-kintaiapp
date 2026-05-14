<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function create()
    {
        // 指定したフォルダ階層のBladeファイルを表示する
        return view('auth.register');
    }

    public function store(Request $request) // ここは普通のRequestにしておく
    {
        // 1. バリデーション（一時的にここに書く）
        $request->validate([
            'name'     => 'required|string|max:20',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        // 2. データの保存
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 2,
        ]);

        return redirect()->route('attendance.index');
    }


}
