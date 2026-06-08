<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class AttendanceStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_is_outside_work_when_no_attendance_record_exists()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 8, 0, 0));
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }

    public function test_status_is_working_when_user_punched_in()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));
        $user = User::factory()->create();

        Attendance::create([
            'user_id'   => $user->id,
            'date'      => '2026-06-07',
            'punch_in'  => '09:00:00',
            'punch_out' => null,
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('出勤中');
    }

    public function test_status_is_break_when_user_is_taking_a_break()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 12, 30, 0));
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id'   => $user->id,
            'date'      => '2026-06-07',
            'punch_in'  => '09:00:00',
            'punch_out' => null,
        ]);

        Rest::create([
            'attendance_id' => $attendance->id,
            'break_in'      => '12:00:00',
            'break_out'     => null,
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('休憩中');
    }

    public function test_status_is_punched_out_when_user_completed_work()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 19, 0, 0));
        $user = User::factory()->create();

        Attendance::create([
            'user_id'   => $user->id,
            'date'      => '2026-06-07',
            'punch_in'  => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('退勤済');
    }
}
