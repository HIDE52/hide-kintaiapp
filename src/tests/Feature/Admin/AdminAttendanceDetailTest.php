<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_detail_displayed_correctly_when_admin_viewed()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $admin = User::factory()->create(['role' => 1]);
        $staff = User::factory()->create(['role' => 2]);

        $attendance = Attendance::factory()->create([
            'user_id'   => $staff->id,
            'date'      => '2026-06-07',
            'punch_in'  => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $rest = Rest::factory()->create([
            'attendance_id' => $attendance->id,
            'break_in'      => '12:00:00',
            'break_out'     => '13:00:00',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/' . $attendance->id);

        $response->assertStatus(200);

        $response->assertSeeInOrder([
            $staff->name,
            '2026年',
            '6月7日',
            '09:00',
            '18:00',
            '12:00',
            '13:00',
        ]);

        Carbon::setTestNow();
    }

    public function test_validation_error_occurs_when_admin_updated_punch_in_after_punch_out()
    {
        $admin = User::factory()->create(['role' => 1]);
        $staff = User::factory()->create(['role' => 2]);
        $attendance = Attendance::factory()->create(['user_id' => $staff->id]);

        $invalidData = [
            'punch_in'  => '19:00',
            'punch_out' => '18:00',
            'remark'    => '修正理由を記載します。',
        ];

        $response = $this->actingAs($admin)->put('/admin/attendance/' . $attendance->id, $invalidData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['punch_out']);
    }

    public function test_validation_error_occurs_when_admin_updated_break_in_after_punch_out()
    {
        $admin = User::factory()->create(['role' => 1]);
        $staff = User::factory()->create(['role' => 2]);

        $attendance = Attendance::factory()->create([
            'user_id'   => $staff->id,
            'punch_in'  => '09:00:00',
            'punch_out' => '18:00:00',
        ]);
        $rest = Rest::factory()->create(['attendance_id' => $attendance->id]);

        $invalidData = [
            'punch_in'  => '09:00',
            'punch_out' => '18:00',
            'rest_id'   => [
                $rest->id => [
                    'break_in'  => '19:00',
                    'break_out' => '19:30',
                ]
            ],
            'remark'    => '修正理由を記載します。',
        ];

        $response = $this->actingAs($admin)->put('/admin/attendance/' . $attendance->id, $invalidData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['rest_id.' . $rest->id . '.break_in']);
    }

    public function test_validation_error_occurs_when_admin_updated_break_out_after_punch_out()
    {
        $admin = User::factory()->create(['role' => 1]);
        $staff = User::factory()->create(['role' => 2]);

        $attendance = Attendance::factory()->create([
            'user_id'   => $staff->id,
            'punch_in'  => '09:00:00',
            'punch_out' => '18:00:00',
        ]);
        $rest = Rest::factory()->create(['attendance_id' => $attendance->id]);

        $invalidData = [
            'punch_in'  => '09:00',
            'punch_out' => '18:00',
            'rest_id'   => [
                $rest->id => [
                    'break_in'  => '12:00',
                    'break_out' => '18:30',
                ]
            ],
            'remark'    => '修正理由を記載します。',
        ];

        $response = $this->actingAs($admin)->put('/admin/attendance/' . $attendance->id, $invalidData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['rest_id.' . $rest->id . '.break_out']);
    }

    public function test_validation_error_occurs_when_admin_updated_remark_blank()
    {
        $admin = User::factory()->create(['role' => 1]);
        $staff = User::factory()->create(['role' => 2]);
        $attendance = Attendance::factory()->create(['user_id' => $staff->id]);

        $invalidData = [
            'punch_in'  => '09:00',
            'punch_out' => '18:00',
            'remark'    => '',
        ];

        $response = $this->actingAs($admin)->put('/admin/attendance/' . $attendance->id, $invalidData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['remark']);
    }
}
