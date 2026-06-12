<?php

// ═══════════════════════════════════════════════════════════════════════════════
//  routes/api.php  - Henkaten Board Secure API Routes
//
//  Security model:
//    • Every route here requires:
//        1. A valid authenticated session     (auth middleware)
//        2. A valid X-App-Secret header       (app.secret / VerifyAppSecret)
//           - Browser clients  → session nonce generated per session
//           - Service clients  → hash_hmac('sha256','henkaten-api',APP_API_SECRET)
//
//    • Laravel's api group also applies VerifyAppSecret globally
//      (registered in bootstrap/app.php), but we add auth explicitly here
//      so that session-authenticated users are recognized.
//
//  Added: 2026-05-10 | Security hardening patch | @RizkyDaffy
// ═══════════════════════════════════════════════════════════════════════════════

use App\Http\Controllers\Admin\AbsenceController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FactoryController;
use App\Http\Controllers\Admin\GlobalLogController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\MachineController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\ReplacementController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\StatusController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\MachineFloorPlanController;
use Illuminate\Support\Facades\Route;

// ─── All /api/* routes require authentication + X-App-Secret ─────────────────
// Note: app.secret is also appended to the 'api' middleware group globally,
// so it applies here even without the explicit alias. Explicit is clearer.
Route::middleware(['auth', 'app.secret', 'sc.guard'])->group(function () {

    // ── Dashboard Status (TV auto-refresh, dashboard polling) ─────────────
    // Rate-limited to protect DB from rapid polling abuse
    Route::get('status', [DashboardController::class, 'statusApi'])
        ->middleware('throttle:120,1');

    Route::post('context', [DashboardController::class, 'setContext']);

    // ── Replacements (Pengganti) ──────────────────────────────────────────
    Route::get('replacements', [ReplacementController::class, 'index']);
    Route::post('replacements', [ReplacementController::class, 'store']);
    Route::delete('replacements/{replacement}', [ReplacementController::class, 'destroy']);

    // ── Problem Logs ──────────────────────────────────────────────────────
    Route::prefix('logs')->group(function () {
        Route::get('list', [LogController::class, 'list']);
        Route::get('combined', [LogController::class, 'combined']);
        Route::post('/', [LogController::class, 'store']);
        Route::patch('{log}/close', [LogController::class, 'close']);
        Route::patch('{log}/reopen', [LogController::class, 'reopen']);
        Route::patch('{log}', [LogController::class, 'update']);
        Route::delete('{log}', [LogController::class, 'destroy']);
    });

    // ── Attendance Data ───────────────────────────────────────────────────
    Route::get('attendance/data', [AttendanceController::class, 'getData']);

    // ── Absence ───────────────────────────────────────────────────────────
    Route::prefix('absence')->group(function () {
        Route::post('save', [AbsenceController::class, 'save']);
        Route::get('data', [AbsenceController::class, 'getData']);
        Route::get('candidates', [AbsenceController::class, 'candidates']);
        Route::get('report', [AbsenceController::class, 'report']);
        Route::post('rebuild-summary', [AbsenceController::class, 'rebuildSummaryEndpoint']);
        Route::get('export', [AbsenceController::class, 'export'])
            ->middleware('throttle:10,1');
        Route::get('export-excel', [AbsenceController::class, 'exportExcel'])
            ->middleware('throttle:10,1');
    });

    // ── Daily Assignment ──────────────────────────────────────────────────
    Route::prefix('assignment')->group(function () {
        Route::post('save', [AssignmentController::class, 'save']);
        Route::get('data', [AssignmentController::class, 'getData']);
        Route::get('candidates', [AssignmentController::class, 'candidates']);
        Route::post('sync-absen', [AssignmentController::class, 'syncAbsen']);
    });

    // ── Members ───────────────────────────────────────────────────────────
    Route::get('members/list', [MemberController::class, 'list']);
    Route::get('members/{member}', [MemberController::class, 'show']);

    // Member CRUD  - admin & gl only
    Route::middleware('role:admin,gl')->prefix('members')->group(function () {
        Route::post('/', [MemberController::class, 'store']);
        Route::put('/{member}', [MemberController::class, 'update']);
        Route::delete('/clear-all', [MemberController::class, 'clearAll']);
        Route::delete('/{member}', [MemberController::class, 'destroy']);
        Route::post('/import', [MemberController::class, 'import']);
        Route::get('/export', [MemberController::class, 'export'])
            ->middleware('throttle:10,1');
        Route::get('/template', [MemberController::class, 'downloadTemplate']);
    });

    // ── Machines ──────────────────────────────────────────────────────────
    Route::prefix('machines')->group(function () {
        // Floor plan data (previously under auth+internal.request)
        Route::get('all', [MachineFloorPlanController::class, 'getAllMachines']);
        Route::get('floor-plan', [MachineFloorPlanController::class, 'getFloorPlanData']);
        Route::get('{id}/floor-plan', [MachineFloorPlanController::class, 'getFloorPlanDataById']);

        // Machine status & photo operations
        Route::post('photo', [MachineController::class, 'uploadPhoto']);
        Route::post('status', [MachineController::class, 'updateStatus']);
        Route::get('lights', [MachineController::class, 'getLights']);
        Route::get('statuses', [MachineController::class, 'getStatuses']);
        Route::patch('{machine}/floor-coordinates', [MachineController::class, 'updateFloorCoordinates']);
    });

    // ── Factory / Section / Status  - admin only ───────────────────────────
    Route::middleware('role:admin')->group(function () {
        // Factories
        Route::prefix('factories')->group(function () {
            Route::post('/', [FactoryController::class, 'store']);
            Route::put('/{factory}', [FactoryController::class, 'update']);
            Route::delete('/{factory}', [FactoryController::class, 'destroy']);
            Route::patch('/reorder', [FactoryController::class, 'reorder']);
        });

        // Sections
        Route::prefix('sections')->group(function () {
            Route::post('/', [SectionController::class, 'store']);
            Route::put('/{section}', [SectionController::class, 'update']);
            Route::delete('/{section}', [SectionController::class, 'destroy']);
            Route::patch('/reorder', [SectionController::class, 'reorder']);
        });

        // Statuses
        Route::prefix('statuses')->group(function () {
            Route::post('/', [StatusController::class, 'store']);
            Route::put('/{status}', [StatusController::class, 'update']);
            Route::delete('/{status}', [StatusController::class, 'destroy']);
        });

        // Users  - admin only CRUD
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::get('/{user}', [UserController::class, 'show']);
            Route::post('/', [UserController::class, 'store']);
            Route::put('/{user}', [UserController::class, 'update']);
            Route::delete('/{user}', [UserController::class, 'destroy']);
        });
    });

    // Sections & Factories read access (available to gl role for dropdowns)
    Route::get('factories', [FactoryController::class, 'apiList']);
    Route::get('sections', [SectionController::class, 'apiList']);
    Route::get('statuses', [StatusController::class, 'apiList']);

    // ── Task 6: Client-side Activity Beacon ────────────────────────────────
    // Accepts UI action events from authenticated browser clients.
    // Behind auth + app.secret (GlobalActivityLogger logs server-side automatically).
    Route::post('activity-log', [GlobalLogController::class, 'storeActivity'])
         ->middleware('throttle:60,1'); // 60 beacons/min max

});
