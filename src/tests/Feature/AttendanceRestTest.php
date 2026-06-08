<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class AttendanceRestTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_changes_to_resting_when_user_punches_rest_start()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 12, 0, 0));
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id'  => $user->id,
            'date'     => '2026-06-07',
            'punch_in' => '09:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('休憩入');

        $restStartResponse = $this->actingAs($user)->post('/attendance/rest/start');
        $restStartResponse->assertRedirect();

        $this->assertDatabaseHas('rests', [
            'attendance_id' => $attendance->id,
            'break_in'      => '12:00:00',
            'break_out'     => null,
        ]);

        $statusResponse = $this->actingAs($user)->get('/attendance');
        $statusResponse->assertSee('休憩中');
    }

    public function test_rest_start_button_is_visible_again_when_user_repeats_rest()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 14, 0, 0));
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id'  => $user->id,
            'date'     => '2026-06-07',
            'punch_in' => '09:00:00',
        ]);

        Rest::create([
            'attendance_id' => $attendance->id,
            'break_in'      => '12:00:00',
            'break_out'     => '13:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('休憩入');

        $this->actingAs($user)->post('/attendance/rest/start');
        $this->assertEquals(2, Rest::where('attendance_id', $attendance->id)->count());
    }

    public function test_status_changes_to_working_when_user_punches_rest_end()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 13, 0, 0));
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id'  => $user->id,
            'date'     => '2026-06-07',
            'punch_in' => '09:00:00',
        ]);

        $rest = Rest::create([
            'attendance_id' => $attendance->id,
            'break_in'      => '12:00:00',
            'break_out'     => null,
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩戻');

        $restEndResponse = $this->actingAs($user)->patch('/attendance/rest/end');
        $restEndResponse->assertRedirect();

        $this->assertDatabaseHas('rests', [
            'id'        => $rest->id,
            'break_out' => '13:00:00',
        ]);

        $statusResponse = $this->actingAs($user)->get('/attendance');
        $statusResponse->assertSee('出勤中');
    }

    public function test_rest_end_button_is_visible_when_user_enters_second_rest()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 15, 30, 0));
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id'  => $user->id,
            'date'     => '2026-06-07',
            'punch_in' => '09:00:00',
        ]);

        Rest::create([
            'attendance_id' => $attendance->id,
            'break_in'      => '12:00:00',
            'break_out'     => '13:00:00',
        ]);

        Rest::create([
            'attendance_id' => $attendance->id,
            'break_in'      => '15:00:00',
            'break_out'     => null,
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩戻');
    }

    public function test_total_rest_time_is_displayed_on_list_when_user_takes_rests()
    {
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id'  => $user->id,
            'date'     => '2026-06-07',
            'punch_in' => '09:00:00',
        ]);

        Rest::create([
            'attendance_id' => $attendance->id,
            'break_in'      => '12:00:00',
            'break_out'     => '12:30:00',
        ]);

        Rest::create([
            'attendance_id' => $attendance->id,
            'break_in'      => '15:00:00',
            'break_out'     => '15:45:00',
        ]);

        $listResponse = $this->actingAs($user)->get('/attendance/list');
        $listResponse->assertStatus(200);
        $listResponse->assertSee('01:15');
    }
}
