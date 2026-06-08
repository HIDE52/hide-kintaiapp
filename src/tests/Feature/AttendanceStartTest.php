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
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('出勤');

        $punchInResponse = $this->actingAs($user)->post('/attendance/start');
        $punchInResponse->assertRedirect();

        $this->assertDatabaseHas('attendances', [
            'user_id'   => $user->id,
            'date'      => '2026-06-07',
            'punch_in'  => '09:00:00',
        ]);

        $statusCheckResponse = $this->actingAs($user)->get('/attendance');
        $statusCheckResponse->assertSee('出勤中');
    }

    public function test_punch_in_button_is_hidden_when_user_already_punched_out()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 18, 0, 0));
        $user = User::factory()->create();

        Attendance::create([
            'user_id'   => $user->id,
            'date'      => '2026-06-07',
            'punch_in'  => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertDontSee('出勤');

        $illegalResponse = $this->actingAs($user)->post('/attendance/start');
        $illegalResponse->assertRedirect();

        $this->assertEquals(1, Attendance::where('user_id', $user->id)->count());
    }

    public function test_punch_in_time_is_displayed_on_list_when_user_punches_in()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 9, 15, 0));
        $user = User::factory()->create();

        $this->actingAs($user)->post('/attendance/start');

        $listResponse = $this->actingAs($user)->get('/attendance/list');
        $listResponse->assertStatus(200);
        $listResponse->assertSee('09:15');
    }
}
