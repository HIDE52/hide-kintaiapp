<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceStartTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_changes_to_working_when_user_punches_in()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 9, 0, 0));

        $staff = User::factory()->create([
            'role' => 2,
        ]);

        $response = $this->actingAs($staff)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('出勤');

        $punchInResponse = $this->actingAs($staff)->post('/attendance/start');
        $punchInResponse->assertRedirect();

        $statusCheckResponse = $this->actingAs($staff)->get('/attendance');
        $statusCheckResponse->assertSee('出勤中');

        Carbon::setTestNow();
    }

    public function test_punch_in_button_is_hidden_when_user_already_punched_out()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 18, 0, 0));

        $staff = User::factory()->create([
            'role' => 2,
        ]);

        Attendance::factory()->create([
            'user_id'   => $staff->id,
            'date'      => '2026-06-07',
            'punch_in'  => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $response = $this->actingAs($staff)->get('/attendance');
        $response->assertStatus(200);
        $response->assertDontSee('class="attendance-stamp__btn attendance-stamp__btn--black">出勤');

        Carbon::setTestNow();
    }

    public function test_illegal_punch_in_request_is_blocked_when_user_already_punched_out()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 18, 0, 0));

        $staff = User::factory()->create([
            'role' => 2,
        ]);

        Attendance::factory()->create([
            'user_id'   => $staff->id,
            'date'      => '2026-06-07',
            'punch_in'  => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $illegalResponse = $this->actingAs($staff)->post('/attendance/start');
        $illegalResponse->assertRedirect();

        $this->assertEquals(1, Attendance::where('user_id', $staff->id)->count());

        Carbon::setTestNow();
    }

    public function test_punch_in_time_is_displayed_on_list_when_user_punches_in()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 9, 15, 0));

        $staff = User::factory()->create([
            'role' => 2,
        ]);

        $this->actingAs($staff)->post('/attendance/start');

        $listResponse = $this->actingAs($staff)->get('/attendance/list');
        $listResponse->assertStatus(200);
        $listResponse->assertSee('09:15');

        Carbon::setTestNow();
    }
}
