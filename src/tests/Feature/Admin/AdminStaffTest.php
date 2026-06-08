<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class AdminStaffTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_staff_credentials_are_visible_when_admin_accesses_staff_list_page()
    {
        $admin = User::factory()->create([
            'name' => '管理者太郎',
            'role' => 1,
        ]);

        $staff1 = User::factory()->create([
            'name' => 'スタッフ一郎',
            'role' => 2,
        ]);

        $staff2 = User::factory()->create([
            'name' => 'スタッフ二郎',
            'role' => 2,
        ]);

        $response = $this->actingAs($admin)->get('/admin/staff/list');

        $response->assertStatus(200);
        $response->assertSee($staff1->name);
        $response->assertSee($staff1->email);
        $response->assertSee($staff2->name);
        $response->assertSee($staff2->email);
    }

    public function test_attendance_and_rest_times_are_correctly_displayed_when_admin_views_staff_attendance_page()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $staff = User::factory()->create([
            'role' => 2,
        ]);

        $attendance = Attendance::factory()->create([
            'user_id'   => $staff->id,
            'date'      => '2026-06-07',
            'punch_in'  => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        Rest::factory()->create([
            'attendance_id' => $attendance->id,
            'break_in'      => '12:00:00',
            'break_out'     => '13:00:00',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/staff/' . $staff->id . '?tab=2026-06');

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');

        Carbon::setTestNow();
    }

    public function test_previous_month_parameter_is_generated_when_admin_views_attendance_page()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $staff = User::factory()->create([
            'role' => 2,
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/staff/' . $staff->id . '?tab=2026-06');

        $response->assertStatus(200);
        $expectedPrevUrl = '/admin/attendance/staff/' . $staff->id . '?tab=2026-05';
        $response->assertSee($expectedPrevUrl);

        Carbon::setTestNow();
    }

    public function test_next_month_parameter_is_generated_when_admin_views_attendance_page()
    {
        Carbon::setTestNow(Carbon::create(2020, 3, 1, 10, 0, 0));

        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $staff = User::factory()->create([
            'role' => 2,
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $staff->id,
            'date'    => '2020-02-15',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/staff/' . $staff->id . '?tab=2020-01');
        $response->assertStatus(200);

        $expectedNextUrl = '/admin/attendance/staff/' . $staff->id . '?tab=2020-02';
        $response->assertSee($expectedNextUrl);

        Carbon::setTestNow();
    }

    public function test_detail_page_link_is_correctly_embedded_when_admin_views_attendance_list()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $staff = User::factory()->create([
            'role' => 2,
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $staff->id,
            'date'    => '2026-06-07',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/staff/' . $staff->id . '?tab=2026-06');

        $response->assertStatus(200);
        $expectedDetailUrl = '/admin/attendance/' . $attendance->id;
        $response->assertSee($expectedDetailUrl);

        Carbon::setTestNow();
    }
}
