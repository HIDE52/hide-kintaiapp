<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_users_attendance_are_displayed_accurately_when_admin_views_list()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $admin = User::factory()->create([
            'name' => '管理者太郎',
            'role' => 1,
        ]);

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

        $response = $this->actingAs($admin)->get('/admin/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('スタッフ一郎');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('01:00');
        $response->assertSee('08:00');
        $response->assertDontSee('管理者太郎');

        Carbon::setTestNow();
    }

    public function test_current_date_is_displayed_by_default_when_admin_opens_list()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 12, 0, 0));

        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list');

        $response->assertStatus(200);
        $response->assertSee('2026/06/07');

        Carbon::setTestNow();
    }

    public function test_previous_day_attendance_is_displayed_when_admin_clicks_prev_button()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 12, 0, 0));

        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $staff = User::factory()->create([
            'name' => 'スタッフ一郎',
            'role' => 2,
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $staff->id,
            'date' => '2026-06-06',
            'punch_in' => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list?date=2026-06-06');

        $response->assertStatus(200);
        $response->assertSee('2026/06/06');
        $response->assertSee('スタッフ一郎');

        Carbon::setTestNow();
    }

    public function test_next_day_attendance_is_displayed_when_admin_clicks_next_button()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 8, 12, 0, 0));

        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list?date=2026-06-08');

        $response->assertStatus(200);
        $response->assertSee('2026/06/08');

        Carbon::setTestNow();
    }
}
