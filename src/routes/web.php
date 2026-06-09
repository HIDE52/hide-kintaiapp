<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CorrectionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\Admin\CorrectionController as AdminCorrectionController;
use App\Http\Controllers\Admin\LoginController as AdminLoginController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CheckEmailVerificationByRole;

Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::middleware(['auth', CheckEmailVerificationByRole::class])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/start', [AttendanceController::class, 'start']);
    Route::patch('/attendance/end', [AttendanceController::class, 'end']);
    Route::post('/attendance/rest/start', [AttendanceController::class, 'restStart']);
    Route::patch('/attendance/rest/end', [AttendanceController::class, 'restEnd']);

    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');

    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::post('/attendance/detail/{id}', [CorrectionController::class, 'store'])->name('correction.store');

    Route::get('/stamp_correction_request/list', [CorrectionController::class, 'index'])->name('request.index');
});

Route::get('/admin/login', [AdminLoginController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login']);

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/attendance/list', [AdminAttendanceController::class, 'index'])->name('admin.attendance.index');

    Route::get('/attendance/{id}', [AdminAttendanceController::class, 'show'])->name('admin.attendance.show');
    Route::put('/attendance/{id}', [AdminAttendanceController::class, 'update'])->name('admin.attendance.update');

    Route::get('/staff/list', [AdminStaffController::class, 'index'])->name('admin.staff.index');

    Route::get('/attendance/staff/{id}', [AdminStaffController::class, 'showStaff'])->name('admin.staff.attendance');
    Route::get('/attendance/export/{id}', [AdminStaffController::class, 'exportCsv'])->name('admin.staff.export');

    Route::get('/stamp_correction_request/list', [AdminCorrectionController::class, 'index'])->name('admin.request.index');

    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminCorrectionController::class, 'showApprove'])->name('admin.correction.approve');
    Route::put('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminCorrectionController::class, 'approve']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
