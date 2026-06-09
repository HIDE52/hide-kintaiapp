<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_error_occurs_when_name_is_empty_on_registration()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 8, 10, 0, 0));

        $response = $this->post('/register', [
            'name'                  => '',
            'email'                 => 'staff1@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください',
        ]);

        Carbon::setTestNow();
    }

    public function test_validation_error_occurs_when_email_is_empty_on_registration()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 8, 10, 0, 0));

        $response = $this->post('/register', [
            'name'                  => 'スタッフ一郎',
            'email'                 => '',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);

        Carbon::setTestNow();
    }

    public function test_validation_error_occurs_when_password_is_less_than_8_characters_on_registration()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 8, 10, 0, 0));

        $response = $this->post('/register', [
            'name'                  => 'スタッフ一郎',
            'email'                 => 'staff1@example.com',
            'password'              => 'pass123',
            'password_confirmation' => 'pass123',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);

        Carbon::setTestNow();
    }

    public function test_validation_error_occurs_when_password_does_not_match_confirmation_on_registration()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 8, 10, 0, 0));

        $response = $this->post('/register', [
            'name'                  => 'スタッフ一郎',
            'email'                 => 'staff1@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'different123',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません',
        ]);

        Carbon::setTestNow();
    }

    public function test_validation_error_occurs_when_password_is_empty_on_registration()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 8, 10, 0, 0));

        $response = $this->post('/register', [
            'name'                  => 'スタッフ一郎',
            'email'                 => 'staff1@example.com',
            'password'              => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);

        Carbon::setTestNow();
    }

    public function test_user_is_saved_successfully_when_valid_registration_data_is_submitted()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 8, 10, 0, 0));

        $response = $this->post('/register', [
            'name'                  => 'スタッフ一郎',
            'email'                 => 'staff1@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'name'  => 'スタッフ一郎',
            'email' => 'staff1@example.com',
            'role'  => 2,
        ]);

        $response->assertRedirect('/attendance');

        Carbon::setTestNow();
    }
}
