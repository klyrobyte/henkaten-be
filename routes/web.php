<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MachineController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\AbsenceController;
use App\Http\Controllers\Admin\AbsenceReasonController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\ReplacementController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MesinManagementController;
use App\Http\Controllers\Admin\FactoryController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\StatusController;
use App\Http\Controllers\Admin\RepairDepartmentController;
use App\Http\Controllers\Admin\SiteConfigController;
use App\Http\Controllers\Admin\ScController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
// Rate limit login to 10 attempts/minute per IP â€” brute-force protection
Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// â”€â”€â”€ Admin â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
Route::middleware(['auth', 'sc.guard'])->prefix('admin')->name('admin.')->group(function () {

     // â”€â”€ Dashboard â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
     Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
     // Rate limit status polling â€” 120/min to support TV board auto-refresh without DoS risk
     // DEPRECATED â€” migrate callers to GET /api/status
     Route::get('/status', [DashboardController::class, 'statusApi'])->name('status')
          ->middleware(['internal.request', 'throttle:120,1', 'log.deprecated']);
     // DEPRECATED â€” migrate callers to POST /api/context
     Route::post('/context', [DashboardController::class, 'setContext'])->name('context')
          ->middleware('log.deprecated');
     Route::post('/set-sc', [DashboardController::class, 'setScContext'])->name('set-sc');

     // â”€â”€ TV MODE â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
     // Picker halaman pilih factory+shift (untuk role tv)
     Route::get('/tv/picker', [DashboardController::class, 'tvPicker'])->name('tv.picker');
     // TV board â€” bisa diakses semua role (tv, admin, tl, dll)
     Route::get('/tv', [DashboardController::class, 'tvMode'])->name('tv');

     // â”€â”€ User Management (Admin only) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
     Route::middleware('role:admin')->prefix('users')->name('users.')->group(function () {
          Route::get('/', [UserController::class, 'index'])->name('index');
          // DEPRECATED â€” migrate callers to GET /api/users/{user}
          Route::get('/{user}', [UserController::class, 'show'])->name('show')
               ->middleware('log.deprecated');
          // DEPRECATED â€” migrate callers to POST /api/users
          Route::post('/', [UserController::class, 'store'])->name('store')
               ->middleware('log.deprecated');
          // DEPRECATED â€” migrate callers to PUT /api/users/{user}
          Route::put('/{user}', [UserController::class, 'update'])->name('update')
               ->middleware('log.deprecated');
          // DEPRECATED â€” migrate callers to DELETE /api/users/{user}
          Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy')
               ->middleware('log.deprecated');
     });

     //   Global Logs (Task 6 — Super Admin Only)               ─
     Route::middleware('role:superadmin')->prefix('global-logs')->name('global-logs.')
          ->group(function () {
               Route::get('/', [\App\Http\Controllers\Admin\GlobalLogController::class, 'index'])->name('index');
               Route::get('/export', [\App\Http\Controllers\Admin\GlobalLogController::class, 'exportCsv'])->name('export')
                    ->middleware('throttle:5,1'); // rate-limit CSV export
          });

     //   SC Management (Super Admin Only)                   ─
     Route::middleware('role:superadmin')->prefix('sc-management')->name('sc.')->group(function () {
          Route::get('/', [ScController::class, 'index'])->name('index');
          Route::post('/', [ScController::class, 'store'])->name('store');
          Route::put('/{sc}', [ScController::class, 'update'])->name('update');
          Route::delete('/{sc}', [ScController::class, 'destroy'])->name('destroy');
     });

     //   Master Data (Super Admin Only)                    ─
     Route::middleware(['role:superadmin', 'global.log'])->prefix('master-data')->name('master-data.')
          ->group(function () {
               Route::get('/', [\App\Http\Controllers\Admin\MasterDataController::class, 'index'])->name('index');
               Route::get('/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'show'])->name('show');
               Route::put('/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'update'])->name('update');
               Route::delete('/delete-all', [\App\Http\Controllers\Admin\MasterDataController::class, 'destroyAll'])->name('destroy-all');
               Route::delete('/{id}', [\App\Http\Controllers\Admin\MasterDataController::class, 'destroy'])->name('destroy');
          });


     // â”€â”€ Mesin â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
     Route::get('machines', [MachineController::class, 'index'])->name('machines.index');
     // DEPRECATED â€” migrate callers to POST /api/machines/photo
     Route::post('machines/photo', [MachineController::class, 'uploadPhoto'])->name('machines.photo')
          ->middleware('log.deprecated');
     // DEPRECATED â€” migrate callers to POST /api/machines/status
     Route::post('machines/status', [MachineController::class, 'updateStatus'])->name('machines.status')
          ->middleware('log.deprecated');
     // DEPRECATED â€” migrate callers to GET /api/machines/lights
     Route::get('machines/lights', [MachineController::class, 'getLights'])->name('machines.lights')
          ->middleware('log.deprecated');
     // DEPRECATED â€” migrate callers to GET /api/machines/statuses
     Route::get('machines/statuses', [MachineController::class, 'getStatuses'])->name('machines.statuses')
          ->middleware('log.deprecated');

     // â”€â”€ Floor Plan Editor â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
     Route::get('machines/floor-plan-editor', [MachineController::class, 'showFloorPlanEditor'])->name('machines.floor-plan-editor');
     // DEPRECATED â€” migrate callers to PATCH /api/machines/{machine}/floor-coordinates
     Route::patch('machines/{machine}/floor-coordinates', [MachineController::class, 'updateFloorCoordinates'])->name('machines.floor-coordinates')
          ->middleware('log.deprecated');

     // â”€â”€ Mesin Management (CRUD) â€” hanya admin & gl â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
     Route::middleware('role:admin,gl')->prefix('mesinmg')->name('mesinmg.')->group(function () {
          Route::get('/', [MesinManagementController::class, 'index'])->name('index');
          Route::post('/', [MesinManagementController::class, 'store'])->name('store');
          Route::get('/{machine}', [MesinManagementController::class, 'show'])->name('show');
          Route::put('/{machine}', [MesinManagementController::class, 'update'])->name('update');
          Route::delete('/{machine}', [MesinManagementController::class, 'destroy'])->name('destroy');
     });

     // â”€â”€ Factory/Section/Status Management (admin only) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
     Route::middleware('role:admin')->group(function () {
          // Pages (non-JSON â€” not deprecated)
          Route::get('group', [FactoryController::class, 'index'])->name('group.index');
          Route::get('absence-reasons', [AbsenceReasonController::class, 'index'])->name('absence-reasons.index');
          Route::post('absence-reasons', [AbsenceReasonController::class, 'store'])->name('absence-reasons.store');
          Route::put('absence-reasons/{absence_reason}', [AbsenceReasonController::class, 'update'])->name('absence-reasons.update');
          Route::delete('absence-reasons/{absence_reason}', [AbsenceReasonController::class, 'destroy'])->name('absence-reasons.destroy');
          Route::get('section', [SectionController::class, 'index'])->name('section.index');
          Route::get('status-management', [StatusController::class, 'index'])->name('status.index');
          Route::get('site-config', [SiteConfigController::class, 'index'])->name('site-config.index');
          Route::post('site-config', [SiteConfigController::class, 'update'])->name('site-config.update');
          Route::post('site-config/reset', [SiteConfigController::class, 'reset'])->name('site-config.reset');
          Route::get('repair-departments', [RepairDepartmentController::class, 'index'])->name('repair-departments.index');
          Route::post('repair-departments', [RepairDepartmentController::class, 'store'])->name('repair-departments.store');
          Route::put('repair-departments/{department}', [RepairDepartmentController::class, 'update'])->name('repair-departments.update');
          Route::delete('repair-departments/{department}', [RepairDepartmentController::class, 'destroy'])->name('repair-departments.destroy');

          // Factory API â€” DEPRECATED, migrate to /api/factories/*
          Route::prefix('api/factories')->name('api.factories.')->group(function () {
               Route::get('/', [FactoryController::class, 'apiList'])->name('list')
                    ->middleware('log.deprecated');
               Route::post('/', [FactoryController::class, 'store'])->name('store')
                    ->middleware('log.deprecated');
               Route::put('/{factory}', [FactoryController::class, 'update'])->name('update')
                    ->middleware('log.deprecated');
               Route::delete('/{factory}', [FactoryController::class, 'destroy'])->name('destroy')
                    ->middleware('log.deprecated');
               Route::patch('/reorder', [FactoryController::class, 'reorder'])->name('reorder')
                    ->middleware('log.deprecated');
          });

          // Section API â€” DEPRECATED, migrate to /api/sections/*
          Route::prefix('api/sections')->name('api.sections.')->group(function () {
               Route::get('/', [SectionController::class, 'apiList'])->name('list')
                    ->middleware('log.deprecated');
               Route::post('/', [SectionController::class, 'store'])->name('store')
                    ->middleware('log.deprecated');
               Route::put('/{section}', [SectionController::class, 'update'])->name('update')
                    ->middleware('log.deprecated');
               Route::delete('/{section}', [SectionController::class, 'destroy'])->name('destroy')
                    ->middleware('log.deprecated');
               Route::patch('/reorder', [SectionController::class, 'reorder'])->name('reorder')
                    ->middleware('log.deprecated');
          });

          // Status API â€” DEPRECATED, migrate to /api/statuses/*
          Route::prefix('api/statuses')->name('api.statuses.')->group(function () {
               Route::get('/', [StatusController::class, 'apiList'])->name('list')
                    ->middleware('log.deprecated');
               Route::post('/', [StatusController::class, 'store'])->name('store')
                    ->middleware('log.deprecated');
               Route::put('/{status}', [StatusController::class, 'update'])->name('update')
                    ->middleware('log.deprecated');
               Route::delete('/{status}', [StatusController::class, 'destroy'])->name('destroy')
                    ->middleware('log.deprecated');
          });
     });

     // â”€â”€ Public section/factory API (accessible by mesinmg for gl role too) â”€â”€
     // DEPRECATED â€” migrate to GET /api/factories, /api/sections, /api/statuses
     Route::get('api/factories', [FactoryController::class, 'apiList'])->name('api.factories.public')
          ->middleware('log.deprecated');
     Route::get('api/sections', [SectionController::class, 'apiList'])->name('api.sections.public')
          ->middleware('log.deprecated');
     Route::get('api/statuses', [StatusController::class, 'apiList'])->name('api.statuses.public')
          ->middleware('log.deprecated');

     // â”€â”€ Pengganti (Assignment Replacements) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
     // DEPRECATED â€” migrate to /api/replacements
     Route::get('replacements', [ReplacementController::class, 'index'])->name('replacements.index')
          ->middleware('log.deprecated');
     Route::post('replacements', [ReplacementController::class, 'store'])->name('replacements.store')
          ->middleware('log.deprecated');
     Route::delete('replacements/{replacement}', [ReplacementController::class, 'destroy'])->name('replacements.destroy')
          ->middleware('log.deprecated');

     // â”€â”€ Problem Log â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
     Route::prefix('logs')->name('logs.')->group(function () {
          Route::get('/', [LogController::class, 'index'])->name('index');  // page â€” not deprecated
          // DEPRECATED â€” migrate JSON-callers to /api/logs/*
          Route::get('/list', [LogController::class, 'list'])->name('list')
               ->middleware('log.deprecated');
          Route::get('/combined', [LogController::class, 'combined'])->name('combined')
               ->middleware('log.deprecated');
          Route::post('/', [LogController::class, 'store'])->name('store')
               ->middleware('log.deprecated');
          Route::patch('/{log}/close', [LogController::class, 'close'])->name('close')
               ->middleware('log.deprecated');
          Route::patch('/{log}/reopen', [LogController::class, 'reopen'])->name('reopen')
               ->middleware('log.deprecated');
          Route::patch('/{log}', [LogController::class, 'update'])->name('update')
               ->middleware('log.deprecated');
          Route::delete('/{log}', [LogController::class, 'destroy'])->name('destroy')
               ->middleware('log.deprecated');
     });

     // â”€â”€ Absensi (lama â€” attendance summary) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
     Route::prefix('attendance')->name('attendance.')->group(function () {
          Route::get('/', [AttendanceController::class, 'index'])->name('index');  // page â€” not deprecated
          Route::post('/', [AttendanceController::class, 'store'])->name('store')
               ->middleware('log.deprecated');   // DEPRECATED â€” migrate to POST /api/attendance
          // DEPRECATED â€” migrate to GET /api/attendance/data
          Route::get('/data', [AttendanceController::class, 'getData'])->name('data')
               ->middleware('log.deprecated');
     });

     // â”€â”€ Laporan â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
     Route::prefix('reports')->name('reports.')->group(function () {
          Route::get('/', [ReportController::class, 'index'])->name('index');
          // Rate limit exports â€” prevent resource exhaustion via rapid export calls
          Route::get('/export', [ReportController::class, 'exportExcel'])->name('export')->middleware('throttle:10,1');
          Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export-excel')->middleware('throttle:10,1');
          Route::get('/backup', [ReportController::class, 'exportJson'])->name('backup')->middleware('throttle:10,1');
     });

     // â”€â”€ Member Management â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
     // Route list (JSON) tetap accessible semua role â€” dipakai dropdown halaman lain
     // DEPRECATED â€” migrate to GET /api/members/list and /api/members/{member}
     Route::get('members/list', [MemberController::class, 'list'])->name('members.list')
          ->middleware('log.deprecated');
     Route::get('members/{member}', [MemberController::class, 'show'])->name('members.show')
          ->middleware('log.deprecated');

     // Sisa routes member â€” hanya admin & gl
     Route::middleware('role:admin,gl')->prefix('members')->name('members.')->group(function () {
          Route::get('/', [MemberController::class, 'index'])->name('index');
          Route::get('/export', [MemberController::class, 'export'])->name('export');
          Route::get('/template', [MemberController::class, 'downloadTemplate'])->name('template');
          // DEPRECATED â€” migrate to DELETE /api/members/clear-all
          Route::delete('/clear-all', [MemberController::class, 'clearAll'])->name('clear-all')
               ->middleware('log.deprecated');
          // DEPRECATED â€” migrate to POST /api/members/import
          Route::post('/import', [MemberController::class, 'import'])->name('import')
               ->middleware('log.deprecated');
          // DEPRECATED â€” migrate to POST /api/members
          Route::post('/', [MemberController::class, 'store'])->name('store')
               ->middleware('log.deprecated');
          // DEPRECATED â€” migrate to PUT /api/members/{member}
          Route::put('/{member}', [MemberController::class, 'update'])->name('update')
               ->middleware('log.deprecated');
          // DEPRECATED â€” migrate to DELETE /api/members/{member}
          Route::delete('/{member}', [MemberController::class, 'destroy'])->name('destroy')
               ->middleware('log.deprecated');
     });

     // â”€â”€ Absensi per-member â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
     Route::prefix('absence')->name('absence.')->group(function () {
          Route::get('/', [AbsenceController::class, 'index'])->name('index');  // page
          // DEPRECATED â€” migrate to POST /api/absence/save
          Route::post('/save', [AbsenceController::class, 'save'])->name('save')
               ->middleware('log.deprecated');
          // DEPRECATED â€” migrate to GET /api/absence/data
          Route::get('/data', [AbsenceController::class, 'getData'])->name('data')
               ->middleware('log.deprecated');
          // DEPRECATED â€” migrate to GET /api/absence/candidates
          Route::get('/candidates', [AbsenceController::class, 'candidates'])->name('candidates')
               ->middleware('log.deprecated');
          Route::get('/report', [AbsenceController::class, 'report'])->name('report');
          // Rate limit exports
          Route::get('/export', [AbsenceController::class, 'export'])->name('export')->middleware('throttle:10,1');
          Route::get('/export-excel', [AbsenceController::class, 'exportExcel'])->name('export-excel')->middleware('throttle:10,1');
          // DEPRECATED â€” migrate to POST /api/absence/rebuild-summary
          Route::post('/rebuild-summary', [AbsenceController::class, 'rebuildSummaryEndpoint'])->name('rebuild-summary')
               ->middleware('log.deprecated');
     });

     // â”€â”€ Penugasan Harian â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
     Route::prefix('assignment')->name('assignment.')->group(function () {
          Route::get('/', [AssignmentController::class, 'index'])->name('index');  // page
          // DEPRECATED â€” migrate to POST /api/assignment/save
          Route::post('/save', [AssignmentController::class, 'save'])->name('save')
               ->middleware('log.deprecated');
          // DEPRECATED â€” migrate to GET /api/assignment/data
          Route::get('/data', [AssignmentController::class, 'getData'])->name('data')
               ->middleware('log.deprecated');
          // DEPRECATED â€” migrate to GET /api/assignment/candidates
          Route::get('/candidates', [AssignmentController::class, 'candidates'])->name('candidates')
               ->middleware('log.deprecated');
          // DEPRECATED â€” migrate to POST /api/assignment/sync-absen
          Route::post('/sync-absen', [AssignmentController::class, 'syncAbsen'])->name('sync-absen')
               ->middleware('log.deprecated');
     });
});

// Redirect root to dashboard
Route::redirect('/', '/admin');

// â”€â”€â”€ Protected Floor Plan Display â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
// Requires authentication â€” moved from public access for security
Route::middleware(['auth'])->group(function () {
     Route::get('/machines/floor-plan', [MachineController::class, 'showFloorPlan'])->name('floor-plan');
});

// â”€â”€â”€ Internal API Routes (DEPRECATED) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
// These routes have been migrated to routes/api.php under /api/* with full
// VerifyAppSecret protection. These old paths remain temporarily for backward
// compatibility. Monitor logs for hits, then remove.
// All routes require: auth + internal.request + throttle + log.deprecated
Route::middleware(['auth', 'internal.request', 'throttle:120,1', 'log.deprecated'])
     ->prefix('api')
     ->group(function () {
          // DEPRECATED â€” migrate to GET /api/machines/all
          Route::get('machines/all', [\App\Http\Controllers\Api\MachineFloorPlanController::class, 'getAllMachines']);
          // DEPRECATED â€” migrate to GET /api/machines/floor-plan
          Route::get('machines/floor-plan', [\App\Http\Controllers\Api\MachineFloorPlanController::class, 'getFloorPlanData']);
          // DEPRECATED â€” migrate to GET /api/machines/{id}/floor-plan
          Route::get('machines/{id}/floor-plan', [\App\Http\Controllers\Api\MachineFloorPlanController::class, 'getFloorPlanDataById']);
     });

