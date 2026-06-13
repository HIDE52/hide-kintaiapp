<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_name_is_login_user_name_when_user_opens_attendance_detail()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $staff = User::factory()->create([
            'name' => 'スタッフ一郎',
            'role' => 2,
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $staff->id,
            'date' => '2026-06-07',
            'punch_in' => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $response = $this->actingAs($staff)->get("/attendance/detail/{$attendance->id}");

        $response->assertStatus(200);
        $response->assertSee('スタッフ一郎');

        Carbon::setTestNow();
    }

    public function test_date_is_selected_date_when_user_opens_attendance_detail()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $staff = User::factory()->create([
            'name' => 'スタッフ一郎',
            'role' => 2,
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $staff->id,
            'date' => '2026-06-07',
            'punch_in' => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $response = $this->actingAs($staff)->get("/attendance/detail/{$attendance->id}");

        $response->assertStatus(200);
        $response->assertSee('2026');

        Carbon::setTestNow();
    }

    public function test_punch_in_and_out_times_match_punches_when_user_opens_attendance_detail()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $staff = User::factory()->create([
            'name' => 'スタッフ一郎',
            'role' => 2,
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $staff->id,
            'date' => '2026-06-07',
            'punch_in' => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $response = $this->actingAs($staff)->get("/attendance/detail/{$attendance->id}");

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');

        Carbon::setTestNow();
    }

    public function test_break_times_match_punches_when_user_opens_attendance_detail()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $staff = User::factory()->create([
            'name' => 'スタッフ一郎',
            'role' => 2,
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $staff->id,
            'date' => '2026-06-07',
            'punch_in' => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        Rest::factory()->create([
            'attendance_id' => $attendance->id,
            'break_in' => '12:00:00',
            'break_out' => '13:00:00',
        ]);

        $response = $this->actingAs($staff)->get("/attendance/detail/{$attendance->id}");

        $response->assertStatus(200);
        $response->assertSee('12:00');
        $response->assertSee('13:00');

        Carbon::setTestNow();
    }
}
