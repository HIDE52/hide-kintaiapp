<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_name_is_required()
    {
        $data = [
            'name'                  => '',
            'email'                 => 'staff@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください',
        ]);
    }

    public function test_email_is_required()
    {
        $data = [
            'name'                  => 'テスト太郎',
            'email'                 => '',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    public function test_password_must_be_at_least_8_characters()
    {
        $data = [
            'name'                  => 'テスト太郎',
            'email'                 => 'staff@example.com',
            'password'              => 'pass123',
            'password_confirmation' => 'pass123',
        ];

        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }

    public function test_password_must_be_confirmed()
    {
        $data = [
            'name'                  => 'テスト太郎',
            'email'                 => 'staff@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'different123',
        ];

        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません',
        ]);
    }

    public function test_password_is_required()
    {
        $data = [
            'name'                  => 'テスト太郎',
            'email'                 => 'staff@example.com',
            'password'              => '',
            'password_confirmation' => '',
        ];

        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    public function test_user_can_register_successfully()
    {
        $data = [
            'name'                  => 'テスト太郎',
            'email'                 => 'staff@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $data);

        $this->assertDatabaseHas('users', [
            'name'  => 'テスト太郎',
            'email' => 'staff@example.com',
            'role'  => 2,
        ]);

        $response->assertRedirect('/attendance');
    }
}
