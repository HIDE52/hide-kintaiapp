<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DeveloperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ==========================================
        // 0. 保存先フォルダの自動作成（スマート版）
        // ==========================================
        // 保存したいフォルダを配列に入れて回すことで、スッキリ記述できます
        $directories = ['profiles', 'items'];


        // ==========================================
        // 1人目：hiroさんの処理
        // ==========================================


        // ユーザー作成
        $hiro = User::create([
            'name' => 'hiro',
            'email' => 'qwerty@gmail.com',
            'password' => Hash::make('zxcvb12345'),
            'role'     => 1,
        ]);


        // ==========================================
        // 2人目：結城 ヒナさんの処理
        // ==========================================

        // ユーザー作成
        $hina = User::create([
            'name' => '結城 ヒナ',
            'email' => 'abcde123@gmail.com',
            'password' => Hash::make('asdf12345'),
            'role'     => 2,
        ]);
    }
}
