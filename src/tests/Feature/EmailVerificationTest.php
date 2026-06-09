<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_email_is_sent_when_user_is_registered()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 8, 10, 0, 0));

        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'スタッフ一郎',
            'email' => 'staff1@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 2,
        ]);

        $staff = User::where('email', 'staff1@example.com')->first();
        $this->assertNotNull($staff);

        Notification::assertSentTo(
            $staff,
            VerifyEmail::class
        );

        Carbon::setTestNow();
    }

    public function test_user_can_navigate_to_mail_verification_site_via_instruction_screen()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 8, 10, 0, 0));

        $staff = User::factory()->unverified()->create([
            'role' => 2,
        ]);

        $response = $this->actingAs($staff)->get('/attendance');
        $response->assertRedirect('/email/verify');

        $verifyPage = $this->actingAs($staff)->get('/email/verify');
        $verifyPage->assertStatus(200);

        $verifyPage->assertSee('認証はこちらから');
        $verifyPage->assertSee('http://localhost:8025');

        Carbon::setTestNow();
    }

    public function test_user_is_redirected_to_attendance_page_when_email_is_verified()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 8, 10, 0, 0));

        $staff = User::factory()->unverified()->create([
            'role' => 2,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $staff->id,
                'hash' => sha1($staff->email),
            ]
        );

        $response = $this->actingAs($staff)->get($verificationUrl);

        $response->assertRedirect('/attendance?verified=1');

        $this->assertNotNull($staff->fresh()->email_verified_at);

        Carbon::setTestNow();
    }
}
