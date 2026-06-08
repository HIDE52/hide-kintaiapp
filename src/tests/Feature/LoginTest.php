<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_email_is_required()
    {
        User::factory()->create([
            'email'    => 'staff@example.com',
            'password' => bcrypt('password123'),
            'role'     => 2,
        ]);

        $loginData = [
            'email'    => '',
            'password' => 'password123',
        ];

        $response = $this->post('/login', $loginData);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    public function test_login_password_is_required()
    {
        User::factory()->create([
            'email'    => 'staff@example.com',
            'password' => bcrypt('password123'),
            'role'     => 2,
        ]);

        $loginData = [
            'email'    => 'staff@example.com',
            'password' => '',
        ];

        $response = $this->post('/login', $loginData);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    public function test_login_credentials_do_not_match()
    {
        User::factory()->create([
            'email'    => 'staff@example.com',
            'password' => bcrypt('password123'),
            'role'     => 2,
        ]);

        $loginData = [
            'email'    => 'wrong-email@example.com',
            'password' => 'password123',
        ];

        $this->flushSession();

        $response = $this->post('/login', $loginData);

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }
}
