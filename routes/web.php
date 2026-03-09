<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MachineController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\AbsenceController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\ReplacementController;
use Illuminate\Support\Facades\Route;

// ─── Auth ────────────────────────────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// ─── Admin ────────────────────────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    // ── Dashboard ────────────────────────────────────────────────────
    Route::get('/',          [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/status',    [DashboardController::class, 'statusApi'])->name('status');
    Route::post('/context',  [DashboardController::class, 'setContext'])->name('context');

    // ── Mesin ────────────────────────────────────────────────────────
    Route::get('machines',         [MachineController::class, 'index'])->name('machines.index');
    Route::post('machines/photo',  [MachineController::class, 'uploadPhoto'])->name('machines.photo');
    Route::post('machines/status', [MachineController::class, 'updateStatus'])->name('machines.status');
    Route::get('machines/lights',  [MachineController::class, 'getLights'])->name('machines.lights');
    Route::get('machines/statuses',[MachineController::class, 'getStatuses'])->name('machines.statuses');
    
    // ── Pengganti (Assignment Replacements) ───────────────────────
    Route::get('replacements',        [ReplacementController::class, 'index'])->name('replacements.index');
    Route::post('replacements',       [ReplacementController::class, 'store'])->name('replacements.store');
    Route::delete('replacements/{replacement}', [ReplacementController::class, 'destroy'])->name('replacements.destroy')    ;


    // ── Problem Log ──────────────────────────────────────────────────
    Route::prefix('logs')->name('logs.')->group(function () {
        Route::get('/',               [LogController::class, 'index'])->name('index');
        Route::get('/list',           [LogController::class, 'list'])->name('list');
        Route::post('/',              [LogController::class, 'store'])->name('store');
        Route::patch('/{log}/close',  [LogController::class, 'close'])->name('close');
        Route::patch('/{log}/reopen', [LogController::class, 'reopen'])->name('reopen');
        Route::patch('/{log}',        [LogController::class, 'update'])->name('update');
        Route::delete('/{log}',       [LogController::class, 'destroy'])->name('destroy');
    });

    // ── Absensi (lama — attendance summary) ──────────────────────────
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/',       [AttendanceController::class, 'index'])->name('index');
        Route::post('/',      [AttendanceController::class, 'store'])->name('store');
        Route::get('/data',   [AttendanceController::class, 'getData'])->name('data');
    });

    // ── Laporan ──────────────────────────────────────────────────────
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',        [ReportController::class, 'index'])->name('index');
        Route::get('/export',  [ReportController::class, 'exportExcel'])->name('export');
        Route::get('/backup',  [ReportController::class, 'exportJson'])->name('backup');
    });

    // ── Member Management ────────────────────────────────────────────
    Route::prefix('members')->name('members.')->group(function () {
        Route::get('/',                [MemberController::class, 'index'])->name('index');
        Route::get('/list',            [MemberController::class, 'list'])->name('list');
        Route::get('/{member}',        [MemberController::class, 'show'])->name('show');
        Route::post('/',               [MemberController::class, 'store'])->name('store');
        Route::put('/{member}',        [MemberController::class, 'update'])->name('update');
        Route::delete('/clear-all',    [MemberController::class, 'clearAll'])->name('clear-all');
        Route::delete('/{member}',     [MemberController::class, 'destroy'])->name('destroy');
        Route::post('/import',         [MemberController::class, 'import'])->name('import');
        Route::get('/export',          [MemberController::class, 'export'])->name('export');
        Route::get('/template',        [MemberController::class, 'downloadTemplate'])->name('template');
    });

    // ── Absensi per-member ───────────────────────────────────────────
    // Termasuk endpoint /candidates yang dipakai panel pengganti di absen.blade.php
    Route::prefix('absence')->name('absence.')->group(function () {
        Route::get('/',            [AbsenceController::class, 'index'])->name('index');
        Route::post('/save',       [AbsenceController::class, 'save'])->name('save');
        Route::get('/data',        [AbsenceController::class, 'getData'])->name('data');
        Route::get('/candidates',  [AbsenceController::class, 'candidates'])->name('candidates'); // ← BARU
        Route::get('/report',      [AbsenceController::class, 'report'])->name('report');
        Route::get('/export',      [AbsenceController::class, 'export'])->name('export');
    });

    // ── Penugasan Harian — tetap ada tapi tidak di bottom nav ────────
    Route::prefix('assignment')->name('assignment.')->group(function () {
        Route::get('/',                   [AssignmentController::class, 'index'])->name('index');
        Route::post('/save',              [AssignmentController::class, 'save'])->name('save');
        Route::get('/data',               [AssignmentController::class, 'getData'])->name('data');
        Route::get('/candidates',         [AssignmentController::class, 'candidates'])->name('candidates');
        Route::post('/sync-absen',        [AssignmentController::class, 'syncAbsen'])->name('sync-absen');
    });
});

// Redirect root ke dashboard
Route::redirect('/', '/admin');