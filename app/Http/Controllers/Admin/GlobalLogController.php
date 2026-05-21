<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlobalLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * GlobalLogController — Task 6 (planning.md 2026-05-20)
 *
 * Dashboard for super_admin only. Displays paginated, decrypted activity logs.
 * No delete/truncate endpoint is exposed — append-only design.
 *
 * @group Global Audit Logs
 * @authenticated
 */
class GlobalLogController extends Controller
{
    /**
     * Display the Global Logs dashboard.
     *
     * Paginates 50 rows per page. Supports filters:
     *   - date_from / date_to  (Y-m-d)
     *   - action               (GET, POST, PATCH, DELETE, UI)
     *   - factory
     *   - role
     *
     * @access super_admin only (enforced by route middleware)
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = GlobalLog::query()->orderBy('created_at', 'desc');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }
        if ($request->filled('action')) {
            $query->where('action', strtoupper($request->input('action')));
        }
        if ($request->filled('factory')) {
            $query->where('factory', $request->input('factory'));
        }
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $logs     = $query->paginate(50)->withQueryString();
        $factories = \App\Models\Factory::orderBy('order_index')->get();

        return view('admin.global_logs', compact('logs', 'factories'));
    }

    /**
     * Export Global Logs as CSV.
     *
     * Downloads a CSV file with decrypted log entries.
     * Only accessible by super_admin.
     *
     * @response 200 scenario="success" {"Content-Type":"text/csv"}
     *
     * @queryParam date_from string Filter start date (Y-m-d). Example: 2026-05-01
     * @queryParam date_to string Filter end date (Y-m-d). Example: 2026-05-20
     * @queryParam action string Filter by action (GET/POST/PATCH/DELETE/UI). Example: POST
     * @queryParam factory string Filter by factory name. Example: Factory 2
     */
    public function exportCsv(Request $request)
    {
        $query = GlobalLog::query()->orderBy('created_at', 'desc');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }
        if ($request->filled('action')) {
            $query->where('action', strtoupper($request->input('action')));
        }
        if ($request->filled('factory')) {
            $query->where('factory', $request->input('factory'));
        }

        $filename = 'global_logs_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel UTF-8 compatibility
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['ID', 'Tanggal', 'Username', 'Role', 'Action', 'Target', 'Factory', 'IP', 'Status', 'Detail']);

            $query->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $log) {
                    $detail = $log->detail ? json_decode($log->detail, true) : [];
                    fputcsv($handle, [
                        $log->id,
                        $log->created_at->format('Y-m-d H:i:s'),
                        GlobalLog::safeDecrypt($log->username_enc),
                        $log->role,
                        $log->action,
                        $log->target,
                        $log->factory,
                        GlobalLog::safeDecrypt($log->ip_enc),
                        $detail['status'] ?? '-',
                        json_encode($detail['params'] ?? [], JSON_UNESCAPED_UNICODE),
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'no-cache, no-store',
            'Pragma'              => 'no-cache',
        ]);
    }

    /**
     * Store a client-side activity beacon.
     *
     * Accepts UI action events from authenticated browser clients.
     * Secured by auth + app.secret middleware (session nonce).
     *
     * @authenticated
     *
     * @bodyParam action string required Event name (e.g. "UI"). Example: UI
     * @bodyParam target string required Page or element description. Example: "clicked:export-csv"
     * @bodyParam factory string optional Current factory context. Example: "Factory 2"
     *
     * @response 200 {"ok": true}
     */
    public function storeActivity(Request $request)
    {
        $request->validate([
            'action' => 'required|string|max:30',
            'target' => 'required|string|max:255',
            'factory' => 'nullable|string|max:100',
        ]);

        $user = Auth::user();
        GlobalLog::record(
            userId:    $user->id ?? null,
            username:  $user->username ?? $user->name ?? null,
            role:      $user->role ?? null,
            action:    $request->input('action'),
            target:    $request->input('target'),
            detail:    ['source' => 'ui_beacon'],
            ip:        $request->ip(),
            userAgent: $request->userAgent(),
            factory:   $request->input('factory') ?? $request->session()->get('factory'),
        );

        return response()->json(['ok' => true]);
    }
}
