<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_email_is_required()
    {
        User::factory()->create([
            'email'    => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role'     => 1,
        ]);

        $loginData = [
            'email'    => '',
            'password' => 'password123',
        ];

        $response = $this->post('/admin/login', $loginData);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    public function test_admin_login_password_is_required()
    {
        User::factory()->create([
            'email'    => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role'     => 1,
        ]);

        $loginData = [
            'email'    => 'admin@example.com',
            'password' => '',
        ];

        $response = $this->post('/admin/login', $loginData);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    public function test_admin_login_credentials_do_not_match()
    {
        User::factory()->create([
            'email'    => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role'     => 1,
        ]);

        $loginData = [
            'email'    => 'wrong-admin@example.com',
            'password' => 'password123',
        ];

        $response = $this->post('/admin/login', $loginData);

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }
}
