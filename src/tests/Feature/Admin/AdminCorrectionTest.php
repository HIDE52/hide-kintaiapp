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
    // 各テスト実行後に自動でデータベースを真っさらにリセットする設定
    use RefreshDatabase;

    /**
     * ID15-1: 承認待ちの修正申請が全て表示されている
     */
    public function test_all_waiting_correction_requests_are_visible_when_admin_accesses_waiting_tab()
    {
        // 1. 日時の固定（2026年6月7日に時間をロック）
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        // 2. テストデータの作成（役割・名前を厳格に指定）
        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $staff1 = User::factory()->create([
            'name' => 'スタッフ一郎',
            'role' => 2,
        ]);

        // AttendanceFactoryのuser_id二重生成を防止して作成
        $attendance = Attendance::factory()->create([
            'user_id' => $staff1->id,
            'date'    => '2026-06-07',
        ]);

        // 承認待ち（status = 0）の申請データを直接記述で作成
        CorrectionAttendance::create([
            'attendance_id'       => $attendance->id,
            'user_id'             => $staff1->id,
            'requested_punch_in'  => '08:30:00',
            'requested_punch_out' => '17:30:00',
            'status'              => 0,
            'remark'              => '電車遅延のため打刻修正',
        ]);

        // 3. 管理者としてログインし、承認待ち一覧画面（tab=waiting）にアクセス
        $response = $this->actingAs($admin)->get('/admin/stamp_correction_request/list?tab=waiting');

        // 4. 検証（画面表示の裏取り。画面の表記「スラッシュ区切り」に合わせて修正）
        $response->assertStatus(200);
        $response->assertSee('スタッフ一郎');
        $response->assertSee('2026/06/07'); // 【修正】ハイフンからUI仕様のスラッシュへ変更

        // 5. 日付固定の解除
        Carbon::setTestNow();
    }

    /**
     * ID15-2: 承認済みの修正申請が全て表示されている
     */
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

        // 承認済み（status = 1）の申請データを直接記述で作成
        CorrectionAttendance::create([
            'attendance_id'       => $attendance->id,
            'user_id'             => $staff2->id,
            'requested_punch_in'  => '09:00:00',
            'requested_punch_out' => '18:00:00',
            'status'              => 1,
            'remark'              => '押し忘れのため修正済',
        ]);

        // 管理者としてログインし、承認済み一覧画面（tab=approved）にアクセス
        $response = $this->actingAs($admin)->get('/admin/stamp_correction_request/list?tab=approved');

        // 画面の表記「スラッシュ区切り」に合わせて検証を修正
        $response->assertStatus(200);
        $response->assertSee('スタッフ二郎');
        $response->assertSee('2026/06/07'); // 【修正】ハイフンからUI仕様のスラッシュへ変更

        Carbon::setTestNow();
    }

    /**
     * ID15-3: 修正申請の詳細内容が正しく表示されている
     */
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

        // 承認待ち申請を作成
        $correction = CorrectionAttendance::create([
            'attendance_id'       => $attendance->id,
            'user_id'             => $staff3->id,
            'requested_punch_in'  => '08:45:00',
            'requested_punch_out' => '17:45:00',
            'status'              => 0,
            'remark'              => '体調不良による遅刻申請',
        ]);

        // 申請に紐づく休憩修正案データを作成
        CorrectionRest::create([
            'attendance_correction_id' => $correction->id,
            'requested_break_in'       => '12:15:00',
            'requested_break_out'      => '13:15:00',
        ]);

        // 詳細（承認）画面へアクセス
        $response = $this->actingAs($admin)->get('/admin/stamp_correction_request/approve/' . $correction->id);

        // 画面上にUI仕様に合わせた「秒数カット（時:分）」の形式でデータが出ているか検証
        $response->assertStatus(200);
        $response->assertSee('スタッフ三郎');
        $response->assertSee('08:45');
        $response->assertSee('17:45');
        $response->assertSee('12:15');
        $response->assertSee('13:15');
        $response->assertSee('体調不良による遅刻申請');

        Carbon::setTestNow();
    }

    /**
     * ID15-4: 修正申請の承認処理が正しく行われる
     */
    public function test_attendance_and_rests_are_updated_and_redirected_when_admin_approves_request()
    {
        Carbon::setTestNow(Carbon::create(2026, 6, 7, 10, 0, 0));

        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $staff1 = User::factory()->create([
            'role' => 2,
        ]);

        // 本番テーブル側の元データ
        $attendance = Attendance::factory()->create([
            'user_id'   => $staff1->id,
            'date'      => '2026-06-07',
            'punch_in'  => '09:00:00',
            'punch_out' => '18:00:00',
        ]);

        // 直接記述スタイルで本番の休憩テーブルを紐付け
        $rest = Rest::factory()->create([
            'attendance_id' => $attendance->id,
            'break_in'      => '12:00:00',
            'break_out'     => '13:00:00',
        ]);

        // これを「こう直したい」という承認待ちの申請データを定義
        $correction = CorrectionAttendance::create([
            'attendance_id'       => $attendance->id,
            'user_id'             => $staff1->id,
            'requested_punch_in'  => '08:30:00',
            'requested_punch_out' => '17:30:00',
            'status'              => 0,
            'remark'              => '打刻ミスのため修正',
        ]);

        // 休憩の修正希望データ
        CorrectionRest::create([
            'attendance_correction_id' => $correction->id,
            'requested_break_in'       => '12:30:00',
            'requested_break_out'      => '13:30:00',
        ]);

        // コントローラのapproveメソッド（PUT）に向けて承認実行リクエストを送信
        $response = $this->actingAs($admin)->put('/admin/stamp_correction_request/approve/' . $correction->id);

        // 一覧画面にリダイレクトされることを検証
        $response->assertRedirect('/admin/stamp_correction_request/list');

        // 本番勤務テーブル（attendances）が、申請通りの時間に上書き更新されたかデータベースを検証
        $this->assertDatabaseHas('attendances', [
            'id'        => $attendance->id,
            'punch_in'  => '08:30:00',
            'punch_out' => '17:30:00',
        ]);

        // 本番休憩テーブル（rests）が、申請通りの時間に上書き更新されたかデータベースを検証
        $this->assertDatabaseHas('rests', [
            'id'        => $rest->id,
            'break_in'  => '12:30:00',
            'break_out' => '13:30:00',
        ]);

        // 申請データのステータスが「1（承認済み）」に変更されたか検証
        $this->assertDatabaseHas('correction_attendances', [
            'id'     => $correction->id,
            'status' => 1,
        ]);

        Carbon::setTestNow();
    }
}
