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

    public function test_own_records_are_visible_when_staff_accesses_attendance_list_page()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $staff = User::factory()->create([
            'role' => 2,
        ]);

        $staff2 = User::factory()->create([
            'role' => 2,
        ]);

        Attendance::create([
            'user_id'   => $staff->id,
            'date'      => '2026-06-01',
            'punch_in'  => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        Attendance::create([
            'user_id'   => $staff2->id,
            'date'      => '2026-06-02',
            'punch_in'  => '10:30:00',
            'punch_out' => '19:30:00',
        ]);

        $response = $this->actingAs($staff)->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertDontSee('10:30');

        Carbon::setTestNow();
    }

    public function test_current_month_is_displayed_when_staff_access_list_page_without_parameter()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $staff = User::factory()->create([
            'role' => 2,
        ]);

        $response = $this->actingAs($staff)->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('2026/06');

        Carbon::setTestNow();
    }

    public function test_display_changes_to_previous_month_when_user_clicks_previous_month_button()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $staff = User::factory()->create([
            'role' => 2,
        ]);

        $response = $this->actingAs($staff)->get('/attendance/list?month=2026-06');
        $response->assertSee('?month=2026-05');

        $prevResponse = $this->get('/attendance/list?month=2026-05');
        $prevResponse->assertSee('2026/05');

        Carbon::setTestNow();
    }

    public function test_display_changes_to_next_month_when_user_clicks_next_month_button()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 15, 10, 0, 0));

        $staff = User::factory()->create([
            'role' => 2,
        ]);

        $response = $this->actingAs($staff)->get('/attendance/list?month=2026-05');
        $response->assertSee('?month=2026-06');

        $nextResponse = $this->get('/attendance/list?month=2026-06');
        $nextResponse->assertSee('2026/06');

        Carbon::setTestNow();
    }

    public function test_detail_page_opens_correctly_when_user_clicks_detail_button()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $staff = User::factory()->create([
            'role' => 2,
        ]);

        $attendance = Attendance::create([
            'user_id'   => $staff->id,
            'date'      => '2026-06-01',
            'punch_in'  => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $response = $this->actingAs($staff)->get('/attendance/list');

        $expectedDetailUrl = '/attendance/detail/' . $attendance->id;
        $response->assertSee($expectedDetailUrl);

        $detailResponse = $this->get($expectedDetailUrl);
        $detailResponse->assertStatus(200);

        Carbon::setTestNow();
    }
}
