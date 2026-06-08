<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_only_own_attendance_records()
    {
        $me = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($me);

        Attendance::create([
            'user_id'   => $me->id,
            'date'      => '2026-06-01',
            'punch_in'  => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        Attendance::create([
            'user_id'   => $other->id,
            'date'      => '2026-06-02',
            'punch_in'  => '10:30:00',
            'punch_out' => '19:30:00',
        ]);

        $response = $this->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertDontSee('10:30');
    }

    public function test_attendance_list_shows_current_month_by_default()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertSee('2026/06');

        Carbon::setTestNow();
    }

    public function test_user_can_navigate_to_previous_month()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/attendance/list?month=2026-06');
        $response->assertSee('?month=2026-05');

        $prevResponse = $this->get('/attendance/list?month=2026-05');
        $prevResponse->assertSee('2026/05');

        Carbon::setTestNow();
    }

    public function test_user_can_navigate_to_next_month()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/attendance/list?month=2026-05');
        $response->assertSee('?month=2026-06');

        $nextResponse = $this->get('/attendance/list?month=2026-06');
        $nextResponse->assertSee('2026/06');

        Carbon::setTestNow();
    }

    public function test_user_can_transition_to_attendance_detail_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendance = Attendance::create([
            'user_id'   => $user->id,
            'date'      => '2026-06-01',
            'punch_in'  => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $response = $this->get('/attendance/list');
        $response->assertSee('/attendance/detail/' . $attendance->id);

        $detailResponse = $this->get('/attendance/detail/' . $attendance->id);
        $detailResponse->assertStatus(200);
    }
}
