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
        $user = User::factory()->create(['role' => 2]);
        $today = Carbon::today()->format('Y-m-d');
        $punchInTime = '09:00:00';

        Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'punch_in' => $punchInTime,
            'punch_out' => null,
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('退勤');
        $response->assertSee('出勤中');

        $leaveTime = '18:00:00';
        Carbon::setTestNow(Carbon::today()->setTime(18, 0, 0));

        $postResponse = $this->actingAs($user)->patch('/attendance/end');
        $postResponse->assertRedirect();
        $postResponse->assertSessionHas('session_success', 'お疲れ様でした。');

        $finalResponse = $this->actingAs($user)->get('/attendance');
        $finalResponse->assertSee('退勤済');
        $finalResponse->assertSee('お疲れ様でした。');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'date' => $today,
            'punch_out' => $leaveTime,
        ]);

        Carbon::setTestNow();
    }

    public function test_leave_time_is_displayed_on_list_after_punching_out()
    {
        $user = User::factory()->create(['role' => 2]);
        $today = Carbon::today()->format('Y-m-d');
        $punchInTime = '09:00:00';
        $punchOutTime = '18:00:00';

        Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'punch_in' => $punchInTime,
            'punch_out' => $punchOutTime,
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertStatus(200);

        $displayTime = '18:00';
        $response->assertSee($displayTime);
    }
}
