<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_error_occurs_when_email_is_empty_on_staff_login()
    {
        User::factory()->create([
            'email'    => 'staff1@example.com',
            'password' => bcrypt('password123'),
            'role'     => 2,
        ]);

        $response = $this->post('/login', [
            'email'    => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    public function test_validation_error_occurs_when_password_is_empty_on_staff_login()
    {
        User::factory()->create([
            'email'    => 'staff1@example.com',
            'password' => bcrypt('password123'),
            'role'     => 2,
        ]);

        $response = $this->post('/login', [
            'email'    => 'staff1@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    public function test_validation_error_occurs_when_invalid_credentials_are_submitted_on_staff_login()
    {
        User::factory()->create([
            'email'    => 'staff1@example.com',
            'password' => bcrypt('password123'),
            'role'     => 2,
        ]);

        $response = $this->post('/login', [
            'email'    => 'wrong-staff@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }
}
