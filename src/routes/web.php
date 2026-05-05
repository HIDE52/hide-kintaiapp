<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CorrectionController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\Admin\CorrectionController as AdminCorrectionController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::middleware(['auth'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/start', [AttendanceController::class, 'start']);
    Route::post('/attendance/end', [AttendanceController::class, 'end']);
    Route::post('/attendance/rest/start', [AttendanceController::class, 'restStart']);
    Route::post('/attendance/rest/end', [AttendanceController::class, 'restEnd']);

    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');

    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::post('/attendance/detail/{id}', [AttendanceController::class, 'update'])->name('attendance.update');

    Route::get('/stamp_correction_request/list', [CorrectionController::class, 'index'])->name('request.index');
// });

// // Route::prefix('admin')->middleware(['auth'])->group(function () {
//     Route::get('/attendance/list', [AdminAttendanceController::class, 'index'])->name('admin.attendance.index');

//     Route::get('/attendance/{id}', [AdminAttendanceController::class, 'show'])->name('admin.attendance.show');
//     Route::post('/attendance/{id}', [AdminAttendanceController::class, 'update'])->name('admin.attendance.update');

//     Route::get('/staff/list', [AdminStaffController::class, 'index'])->name('admin.staff.index');

//     Route::get('/attendance/staff/{id}', [AdminAttendanceController::class, 'showStaff'])->name('admin.staff.attendance');
//     Route::get('/attendance/export/{id}', [AdminAttendanceController::class, 'exportCsv']);

//     Route::get('/stamp_correction_request/list', [AdminCorrectionController::class, 'index'])->name('admin.request.index');

//     Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminCorrectionController::class, 'showApprove']);
//     Route::post('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminCorrectionController::class, 'approve']);
// // });

// 勤怠一覧画面（管理者）
Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'index'])->name('admin.attendance.index');

// 勤怠詳細画面（管理者）
Route::get('/admin/attendance/{id}', [AdminAttendanceController::class, 'show'])->name('admin.attendance.show');
Route::post('/admin/attendance/{id}', [AdminAttendanceController::class, 'update'])->name('admin.attendance.update');

// スタッフ一覧画面
Route::get('/admin/staff/list', [AdminStaffController::class, 'index'])->name('admin.staff.index');

// スタッフ別勤怠一覧画面
Route::get('/admin/attendance/staff/{id}', [AdminAttendanceController::class, 'showStaff'])->name('admin.staff.attendance');

// CSV出力
Route::get('/admin/attendance/export/{id}', [AdminAttendanceController::class, 'exportCsv']);

// 申請一覧画面（管理者）
Route::get('/admin/stamp_correction_request/list', [AdminCorrectionController::class, 'index'])->name('admin.request.index');

// 修正申請承認画面
Route::get('/admin/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminCorrectionController::class, 'showApprove']);
Route::post('/admin/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminCorrectionController::class, 'approve']);