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

        foreach ($directories as $dir) {
            if (!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }
        }

        // ==========================================
        // 1人目：hiroさんの処理
        // ==========================================

        // --- プロフィール画像 ---
        $profilePath1 = 'profiles/user_01.jpg'; // 保存後の名前
        $hiroLocalSource = base_path('private_materials/user_hiro.jpg'); // 元画像

        if (file_exists($hiroLocalSource)) {
            $hiroContents = file_get_contents($hiroLocalSource);
            Storage::disk('public')->put($profilePath1, $hiroContents);
        }

        // ユーザー作成
        $hiro = User::create([
            'name' => 'hiro',
            'email' => 'qwerty@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('zxcvb12345'),
            'postcode' => '156-0044',
            'address' => '東京都世田谷区赤堤7-8-9',
            'img_url' => $profilePath1,
        ]);

        // --- 商品1（赤い車） ---
        $itemPath1 = 'items/item_01.png';
        $carLocalSource = base_path('private_materials/car.png');

        if (file_exists($carLocalSource)) {
            $carContents = file_get_contents($carLocalSource);
            Storage::disk('public')->put($itemPath1, $carContents);
        }

        // カテゴリー取得と商品作成
        $cat1_1 = Category::where('content', 'メンズ')->first();
        $cat1_2 = Category::where('content', 'おもちゃ')->first();
        $cat1_3 = Category::where('content', 'ベビー・キッズ')->first();

        $item1 = Item::create([
            'user_id' => $hiro->id,
            'name'    => 'プラモデル（車）',
            'price'   => 3000,
            'description' => 'スタイリッシュな赤いスポーツカーです。',
            'img_url' => $itemPath1,
            'condition'  => '良好',
        ]);

        if ($cat1_1 && $cat1_2 && $cat1_3) {
            $item1->categories()->attach([$cat1_1->id, $cat1_2->id, $cat1_3->id]);
        }

        // ==========================================
        // 2人目：結城 ヒナさんの処理
        // ==========================================

        // --- プロフィール画像 ---
        $profilePath2 = 'profiles/user_02.jpg';
        $hinaLocalSource = base_path('private_materials/user_hina.jpg');

        if (file_exists($hinaLocalSource)) {
            $hinaContents = file_get_contents($hinaLocalSource);
            Storage::disk('public')->put($profilePath2, $hinaContents);
        }

        // ユーザー作成
        $hina = User::create([
            'name' => '結城 ヒナ',
            'email' => 'abcde123@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('asdf12345'),
            'postcode' => '234-5678',
            'address' => '東京都渋谷区千駄ヶ谷5-6-8',
            'img_url' => $profilePath2,
        ]);

        // --- 商品2（ぬいぐるみ） ---
        $itemPath2 = 'items/item_02.png';
        $bearLocalSource = base_path('private_materials/bear.png');

        if (file_exists($bearLocalSource)) {
            $bearContents = file_get_contents($bearLocalSource);
            Storage::disk('public')->put($itemPath2, $bearContents);
        }

        // カテゴリー取得と商品作成
        $cat2_1 = Category::where('content', 'レディース')->first();
        $cat2_2 = Category::where('content', 'おもちゃ')->first();

        $item2 = Item::create([
            'user_id' => $hina->id,
            'name'    => 'ぬいぐるみ（クマ）',
            'price'   => 1500,
            'description' => '色は白です',
            'img_url' => $itemPath2,
            'condition'  => '良好',
        ]);

        if ($cat2_1 && $cat2_2) {
            $item2->categories()->attach([$cat2_1->id, $cat2_2->id]);
        }
    }
}
