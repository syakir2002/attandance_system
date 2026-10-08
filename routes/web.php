<?php

use App\Http\Controllers\Admin\AttendanceManagementController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\StaffManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Staff\AttendanceController as StaffAttendanceController;
use App\Http\Controllers\Staff\ProfileController as StaffProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('staff.dashboard');
    }

    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.request');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
    Route::redirect('/forgot-password', '/reset-password');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Staff Routes
Route::prefix('staff')
    ->name('staff.')
    ->middleware(['auth', 'role:staff'])
    ->group(function () {
        Route::get('/dashboard', [StaffAttendanceController::class, 'dashboard'])->name('dashboard');
        Route::post('/attendance/clock-in', [StaffAttendanceController::class, 'clockIn'])->name('attendance.clock_in');
        Route::post('/attendance/clock-out', [StaffAttendanceController::class, 'clockOut'])->name('attendance.clock_out');
        Route::get('/attendance', [StaffAttendanceController::class, 'history'])->name('attendance.history');
        Route::get('/profile', [StaffProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile', [StaffProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [StaffProfileController::class, 'updatePassword'])->name('profile.password');
    });

// Admin Routes
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [AdminProfileController::class, 'show'])->name('profile.show');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
        Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
        Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
        Route::post('/calendar/events', [CalendarController::class, 'store'])->name('calendar.store');
        Route::delete('/calendar/events/{id}', [CalendarController::class, 'destroy'])->name('calendar.destroy');

        // Staff CRUD
        Route::get('/staff', [StaffManagementController::class, 'index'])->name('staff.index');
        Route::post('/staff', [StaffManagementController::class, 'store'])->name('staff.store');
        Route::put('/staff/{staff_id}', [StaffManagementController::class, 'update'])->name('staff.update');
        Route::post('/staff/{staff_id}/reset-password', [StaffManagementController::class, 'resetPassword'])->name('staff.reset_password');
        Route::delete('/staff/{staff_id}', [StaffManagementController::class, 'destroy'])->name('staff.destroy');

        // Attendance Management & Correction
        Route::get('/attendance', [AttendanceManagementController::class, 'index'])->name('attendance.index');
        Route::post('/attendance', [AttendanceManagementController::class, 'store'])->name('attendance.store');
        Route::put('/attendance/{id}', [AttendanceManagementController::class, 'update'])->name('attendance.update');
        Route::delete('/attendance/{id}', [AttendanceManagementController::class, 'destroy'])->name('attendance.destroy');

        // Monthly Summary Report
        Route::get('/attendance/summary', [AttendanceManagementController::class, 'summary'])->name('attendance.summary');
    });
