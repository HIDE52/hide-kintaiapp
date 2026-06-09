<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTimeDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_date_and_time_are_displayed_correctly_on_attendance_page()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 5, 7, 0));

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('2026年06月07日(日)');
        $response->assertSee('05:07');

        Carbon::setTestNow();
    }
}
