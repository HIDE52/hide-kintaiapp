<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceLeaveTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_changes_to_left_when_user_punches_leave()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 18, 0, 0));

        $staff = User::factory()->create([
            'role' => 2,
        ]);

        $attendance = Attendance::factory()->create([
            'user_id'   => $staff->id,
            'date'      => '2026-06-07',
            'punch_in'  => '09:00:00',
            'punch_out' => null,
        ]);

        $response = $this->actingAs($staff)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('退勤');
        $response->assertSee('出勤中');

        $postResponse = $this->actingAs($staff)->patch('/attendance/end');
        $postResponse->assertRedirect();

        $finalResponse = $this->actingAs($staff)->get('/attendance');
        $finalResponse->assertSee('退勤済');

        $this->assertDatabaseHas('attendances', [
            'id'        => $attendance->id,
            'user_id'   => $staff->id,
            'punch_out' => '18:00:00',
        ]);

        Carbon::setTestNow();
    }

    public function test_leave_time_is_displayed_on_list_after_punching_out()
    {
        $staff = User::factory()->create([
            'role' => 2,
        ]);

        $attendance = Attendance::factory()->create([
            'user_id'   => $staff->id,
            'date'      => '2026-06-07',
            'punch_in'  => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $response = $this->actingAs($staff)->get('/attendance/list');
        $response->assertStatus(200);
        $response->assertSee('18:00');
    }
}
