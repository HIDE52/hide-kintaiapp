<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Rest;
use App\Models\CorrectionAttendance;
use App\Models\CorrectionRest;
use Carbon\Carbon;

class AttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_punch_out_after_error_message_is_displayed_when_punch_in_is_after_punch_out()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 2
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-07',
            'punch_in' => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->post("/attendance/detail/{$attendance->id}", [
            'punch_in' => '19:00',
            'punch_out' => '18:00',
            'remark' => '出勤時間を遅く修正申請します。',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'punch_out' => '出勤時間もしくは退勤時間が不適切な値です'
        ]);

        Carbon::setTestNow();
    }

    public function test_break_in_before_error_message_is_displayed_when_break_in_is_after_punch_out()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 2
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-07',
            'punch_in' => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->post("/attendance/detail/{$attendance->id}", [
            'punch_in' => '09:00',
            'punch_out' => '18:00',
            'rest_id' => [
                0 => ['break_in' => '19:00', 'break_out' => '19:30']
            ],
            'remark' => '休憩時間の修正申請です。',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'rest_id.0.break_in' => '休憩時間が不適切な値です'
        ]);

        Carbon::setTestNow();
    }

    public function test_break_out_before_error_message_is_displayed_when_break_out_is_after_punch_out()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 2
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-07',
            'punch_in' => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->post("/attendance/detail/{$attendance->id}", [
            'punch_in' => '09:00',
            'punch_out' => '18:00',
            'rest_id' => [
                0 => ['break_in' => '12:00', 'break_out' => '19:00']
            ],
            'remark' => '休憩終了時間を退勤後に設定。',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'rest_id.0.break_out' => '休憩時間もしくは退勤時間が不適切な値です'
        ]);

        Carbon::setTestNow();
    }

    public function test_remark_required_error_message_is_displayed_when_remark_is_empty()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 2
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-07',
            'punch_in' => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->post("/attendance/detail/{$attendance->id}", [
            'punch_in' => '09:00',
            'punch_out' => '18:00',
            'remark' => '',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'remark' => '備考を記入してください'
        ]);

        Carbon::setTestNow();
    }

    public function test_correction_request_is_saved_successfully_when_valid_data_is_submitted()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 2
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-07',
            'punch_in' => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->post("/attendance/detail/{$attendance->id}", [
            'punch_in' => '09:15',
            'punch_out' => '18:15',
            'rest_id' => [
                0 => ['break_in' => '12:00', 'break_out' => '13:00']
            ],
            'remark' => '正当な修正理由をここに記述します。',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('session_success', '修正申請を提出しました。');

        $this->assertDatabaseHas('correction_attendances', [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'requested_punch_in' => '09:15:00',
            'requested_punch_out' => '18:15:00',
            'status' => 0,
            'remark' => '正当な修正理由をここに記述します。'
        ]);

        Carbon::setTestNow();
    }

    public function test_pending_requests_are_displayed_when_user_opens_request_list()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 2
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-07',
            'punch_in' => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        CorrectionAttendance::create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'requested_punch_in' => '09:30:00',
            'requested_punch_out' => '18:30:00',
            'status' => 0,
            'remark' => '承認待ち表示テスト用の備考データ',
        ]);

        $response = $this->actingAs($user)->get('/stamp_correction_request/list?tab=waiting');

        $response->assertStatus(200);
        $response->assertSee('承認待ち表示テスト用の備考データ');

        Carbon::setTestNow();
    }

    public function test_approved_requests_are_displayed_when_user_opens_approved_tab()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 2
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-07',
            'punch_in' => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        CorrectionAttendance::create([
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'requested_punch_in' => '09:45:00',
            'requested_punch_out' => '18:45:00',
            'status' => 1,
            'remark' => '承認済み表示テスト用の備考データ',
        ]);

        $response = $this->actingAs($user)->get('/stamp_correction_request/list?tab=approved');

        $response->assertStatus(200);
        $response->assertSee('承認済み表示テスト用の備考データ');

        Carbon::setTestNow();
    }

    public function test_redirects_to_detail_page_when_user_clicks_request_detail_button()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 2
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-07',
            'punch_in' => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->get("/attendance/detail/{$attendance->id}");

        $response->assertStatus(200);
        $response->assertSee('勤怠詳細');

        Carbon::setTestNow();
    }
}
