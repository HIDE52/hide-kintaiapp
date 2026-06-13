<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\CorrectionAttendance;
use App\Models\CorrectionRest;
use Carbon\Carbon;

class AdminCorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_waiting_correction_requests_are_visible_when_admin_accesses_waiting_tab()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $staff1 = User::factory()->create([
            'name' => 'スタッフ一郎',
            'role' => 2,
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $staff1->id,
            'date'    => '2026-06-07',
        ]);

        CorrectionAttendance::create([
            'attendance_id'       => $attendance->id,
            'user_id'             => $staff1->id,
            'requested_punch_in'  => '08:30:00',
            'requested_punch_out' => '17:30:00',
            'status'              => 0,
            'remark'              => '電車遅延のため打刻修正',
        ]);

        $response = $this->actingAs($admin)->get('/admin/stamp_correction_request/list?tab=waiting');

        $response->assertStatus(200);
        $response->assertSee('スタッフ一郎');
        $response->assertSee('2026/06/07');

        Carbon::setTestNow();
    }

    public function test_all_approved_correction_requests_are_visible_when_admin_accesses_approved_tab()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $staff2 = User::factory()->create([
            'name' => 'スタッフ二郎',
            'role' => 2,
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $staff2->id,
            'date'    => '2026-06-07',
        ]);

        CorrectionAttendance::create([
            'attendance_id'       => $attendance->id,
            'user_id'             => $staff2->id,
            'requested_punch_in'  => '09:00:00',
            'requested_punch_out' => '18:00:00',
            'status'              => 1,
            'remark'              => '押し忘れのため修正済',
        ]);

        $response = $this->actingAs($admin)->get('/admin/stamp_correction_request/list?tab=approved');

        $response->assertStatus(200);
        $response->assertSee('スタッフ二郎');
        $response->assertSee('2026/06/07');

        Carbon::setTestNow();
    }

    public function test_correction_request_details_are_correctly_displayed_when_admin_views_approve_page()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $staff3 = User::factory()->create([
            'name' => 'スタッフ三郎',
            'role' => 2,
        ]);

        $attendance = Attendance::factory()->create([
            'user_id' => $staff3->id,
            'date'    => '2026-06-07',
        ]);

        $correction = CorrectionAttendance::create([
            'attendance_id'       => $attendance->id,
            'user_id'             => $staff3->id,
            'requested_punch_in'  => '08:45:00',
            'requested_punch_out' => '17:45:00',
            'status'              => 0,
            'remark'              => '体調不良による遅刻申請',
        ]);

        CorrectionRest::create([
            'attendance_correction_id' => $correction->id,
            'requested_break_in'       => '12:15:00',
            'requested_break_out'      => '13:15:00',
        ]);

        $response = $this->actingAs($admin)->get('/admin/stamp_correction_request/approve/' . $correction->id);

        $response->assertStatus(200);
        $response->assertSee('スタッフ三郎');
        $response->assertSee('08:45');
        $response->assertSee('17:45');
        $response->assertSee('12:15');
        $response->assertSee('13:15');
        $response->assertSee('体調不良による遅刻申請');

        Carbon::setTestNow();
    }

    public function test_attendance_and_rests_are_updated_and_redirected_when_admin_approves_request()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $staff1 = User::factory()->create([
            'role' => 2,
        ]);

        $attendance = Attendance::factory()->create([
            'user_id'   => $staff1->id,
            'date'      => '2026-06-07',
            'punch_in'  => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $rest = Rest::factory()->create([
            'attendance_id' => $attendance->id,
            'break_in'      => '12:00:00',
            'break_out'     => '13:00:00',
        ]);

        $correction = CorrectionAttendance::create([
            'attendance_id'       => $attendance->id,
            'user_id'             => $staff1->id,
            'requested_punch_in'  => '08:30:00',
            'requested_punch_out' => '17:30:00',
            'status'              => 0,
            'remark'              => '打刻ミスのため修正',
        ]);

        CorrectionRest::create([
            'attendance_correction_id' => $correction->id,
            'requested_break_in'       => '12:30:00',
            'requested_break_out'      => '13:30:00',
        ]);

        $response = $this->actingAs($admin)->put('/admin/stamp_correction_request/approve/' . $correction->id);

        $response->assertRedirect('/admin/stamp_correction_request/list');

        $this->assertDatabaseHas('attendances', [
            'id'        => $attendance->id,
            'punch_in'  => '08:30:00',
            'punch_out' => '17:30:00',
        ]);

        $this->assertDatabaseHas('rests', [
            'attendance_id' => $attendance->id,
            'break_in'  => '12:30:00',
            'break_out' => '13:30:00',
        ]);

        $this->assertDatabaseHas('correction_attendances', [
            'id'     => $correction->id,
            'status' => 1,
        ]);

        Carbon::setTestNow();
    }
}
