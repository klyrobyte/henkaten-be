<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProblemLog;
use App\Services\FactoryConfigService;
use App\Services\ScContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Log
 * 
 * APIs for managing Log.
 */
class LogController extends Controller
{
    public function __construct(protected FactoryConfigService $factoryConfig)
    {
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $scId = ScContext::id();
        $factory = $request->session()->get('factory', ScContext::firstFactory());

        if (!$user->isSuperAdmin() && !empty($user->factory)) {
            $allowedFactories = (array) $user->factory;
            if (!in_array($factory, $allowedFactories)) {
                $factory = $allowedFactories[0];
            }
        }

        $shift = $request->session()->get('shift', 'A');
        $tanggal = $request->get('tanggal', today()->toDateString());

        $logs = ProblemLog::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
        ])->latest()->get();

        $mesinList = $this->factoryConfig->getAllMachines($factory);
        $repairDepartments = \App\Models\RepairDepartment::where('sc_id', $scId)->get();

        return view('admin.log', compact('logs', 'mesinList', 'factory', 'shift', 'tanggal', 'repairDepartments'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $scId = ScContext::id();
        if (!$user->isSuperAdmin() && !empty($user->factory)) {
            $allowedFactories = (array) $user->factory;
            if (!in_array($request->factory, $allowedFactories)) {
                $request->merge(['factory' => $allowedFactories[0]]);
            }
        }

        $request->validate([
            'tanggal' => 'required|date',
            'factory' => 'required|string',
            'shift' => 'required|in:A,B',
            'jenis' => 'required|in:Machine,Material,Method',
            'lokasi' => 'required|string',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
            'status' => 'required|in:open,closed',
            'deskripsi' => 'required|string',
            'cause' => 'nullable|string',
            'countermeasure' => 'nullable|string',
            'pic' => 'nullable|string',
            'departemen_perbaikan' => 'nullable|string',
        ]);

        $waktuSelesai = $request->waktu_selesai;
        if ($request->status === 'closed' && empty($waktuSelesai)) {
            $waktuSelesai = now('Asia/Jakarta')->format('H:i');
        }

        $durasi = null;
        if (!empty($waktuSelesai)) {
            $durasi = $this->calcDuration($request->waktu_mulai, $waktuSelesai);
        }

        $log = ProblemLog::create([
            'sc_id' => $scId,
            'tanggal' => $request->tanggal,
            'factory' => $request->factory,
            'shift' => $request->shift,
            'jenis' => $request->jenis,
            'lokasi' => $request->lokasi,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $waktuSelesai ?: null,
            'status' => $request->status,
            'deskripsi' => $request->deskripsi,
            'cause' => $request->cause ?: null,
            'countermeasure' => $request->countermeasure ?: null,
            'pic' => $request->pic ?: null,
            'durasi' => $durasi,
            'created_by' => auth()->id() ? (int) auth()->id() : null,
            'departemen_perbaikan' => $request->departemen_perbaikan ?: null,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'log' => $log]);
        }

        return back()->with('success', "{$request->jenis} log ditambah: {$request->lokasi}");
    }

    public function close(Request $request, ProblemLog $log)
    {
        $user = Auth::user();
        $scId = ScContext::id();

        if ($log->sc_id != $scId) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized SC access.'], 403);
        }

        if (!$user->isSuperAdmin() && !empty($user->factory)) {
            if (!in_array($log->factory, (array) $user->factory)) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
            }
        }

        $request->validate([
            'countermeasure' => 'required|string|max:1000',
            'waktu_selesai' => 'nullable|date_format:H:i',
        ]);

        $waktuSelesai = $request->waktu_selesai ?? now('Asia/Jakarta')->format('H:i');
        $durasi = $this->calcDuration($log->waktu_mulai, $waktuSelesai);

        $log->update([
            'waktu_selesai' => $waktuSelesai,
            'status' => 'closed',
            'durasi' => $durasi,
            'countermeasure' => $request->countermeasure,
        ]);

        return response()->json([
            'ok' => true,
            'durasi' => $durasi,
            'waktu_selesai' => $waktuSelesai,
            'log' => $log->fresh(),
        ]);
    }

    public function reopen(ProblemLog $log)
    {
        $user = Auth::user();
        $scId = ScContext::id();

        if ($log->sc_id != $scId) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized SC access.'], 403);
        }

        if (!$user->isSuperAdmin() && !empty($user->factory)) {
            if (!in_array($log->factory, (array) $user->factory)) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
            }
        }

        $log->update([
            'waktu_selesai' => null,
            'status' => 'open',
            'durasi' => null,
        ]);

        return response()->json(['ok' => true]);
    }

    public function update(Request $request, ProblemLog $log)
    {
        $user = Auth::user();
        $scId = ScContext::id();

        if ($log->sc_id != $scId) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized SC access.'], 403);
        }

        if (!$user->isSuperAdmin() && !empty($user->factory)) {
            if (!in_array($log->factory, (array) $user->factory)) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
            }
        }

        $request->validate([
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
        ]);

        $durasi = null;
        if ($request->waktu_selesai) {
            $durasi = $this->calcDuration($request->waktu_mulai, $request->waktu_selesai);
        }

        $log->update([
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'durasi' => $durasi,
        ]);

        return response()->json(['ok' => true, 'durasi' => $durasi]);
    }

    public function destroy(ProblemLog $log)
    {
        $user = Auth::user();
        $scId = ScContext::id();

        if ($log->sc_id != $scId) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized SC access.'], 403);
        }

        if (!$user->isSuperAdmin() && !empty($user->factory)) {
            if (!in_array($log->factory, (array) $user->factory)) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized'], 403);
            }
        }

        $log->delete();
        return response()->json(['ok' => true]);
    }

    public function list(Request $request)
    {
        $user = Auth::user();
        $scId = ScContext::id();
        $factory = $request->factory;

        if (!$user->isSuperAdmin() && !empty($user->factory)) {
            $allowedFactories = (array) $user->factory;
            if (!in_array($factory, $allowedFactories)) {
                $factory = $allowedFactories[0];
            }
        }

        $query = ProblemLog::where('sc_id', $scId)->where('factory', $factory);

        if ($request->has('history') && $request->history === '3months') {
            // TV Mode: All active (open) for factory + closed for last 3 months
            $startDate = now()->subMonths(2)->startOfMonth()->toDateString();

            $logs = $query->where(function ($q) use ($startDate) {
                $q->where('status', 'open')
                    ->orWhere(function ($sub) use ($startDate) {
                        $sub->where('status', 'closed')
                            ->where('tanggal', '>=', $startDate);
                    });
            })->orderBy('status', 'desc')->orderBy('waktu_mulai', 'desc')->get();
        } else {
            // Default Mode: Strict tanggal & shift filtering (for Dashboard etc.)
            $logs = $query->where([
                'tanggal' => $request->tanggal,
                'shift' => $request->shift,
            ])->orderBy('waktu_mulai')->get();
        }

        return response()->json($logs);
    }

    /**
     * Combined problem list for TV panel:
     * 3M ProblemLog rows PLUS Man (absen) rows.
     * Logic mirrors the report CSV / ReportController:
     *   - Absen WITHOUT replacement → status = open   (OPEN GOING)
     *   - Absen WITH    replacement → status = closed (DONE)
     *   - 3M logs pass through as-is
     * Returned rows shape:
     *   id, jenis, lokasi, waktu_mulai, waktu_selesai, durasi,
     *   deskripsi, status, cause, countermeasure, pic, _source (log|absen)
     * Bug Fixed by rizky
     */
    public function combined(Request $request)
    {
        $user = Auth::user();
        $scId = ScContext::id();
        $factory = $request->factory;

        if (!$user->isSuperAdmin() && !empty($user->factory)) {
            $allowedFactories = (array) $user->factory;
            if (!in_array($factory, $allowedFactories)) {
                $factory = $allowedFactories[0];
            }
        }

        // Determine if TV mode (history=3months)
        $isTvMode = $request->has('history') && $request->history === '3months';
        $startDate = now()->subMonths(2)->startOfMonth()->toDateString();

        //   3M Problem Logs                        
        $logQuery = ProblemLog::where('sc_id', $scId)->where('factory', $factory)->whereIn('jenis', ['Machine', 'Material', 'Method']);

        if ($isTvMode) {
            $logQuery->where(function ($q) use ($startDate) {
                $q->where('status', 'open') // ALL ongoing open problems MUST be kept until closed
                    ->orWhere(function ($sub) use ($startDate) {
                        $sub->where('status', 'closed')->where('tanggal', '>=', $startDate);
                    });
            });
        } else {
            $logQuery->where(['tanggal' => $request->tanggal, 'shift' => $request->shift]);
        }

        $logs = $logQuery->orderBy('tanggal', 'desc')->orderBy('waktu_mulai', 'desc')->get()->map(fn($l) => [
            'id' => $l->id,
            'jenis' => $l->jenis,
            'lokasi' => $l->lokasi,
            'tanggal' => $l->tanggal,
            'waktu_mulai' => $l->waktu_mulai,
            'waktu_selesai' => $l->waktu_selesai,
            'durasi' => $l->durasi,
            'deskripsi' => $l->deskripsi,
            'status' => $l->status,          // 'open' | 'closed'
            'cause' => $l->cause,
            'countermeasure' => $l->countermeasure,
            'pic' => $l->pic,
            '_source' => 'log',
        ]);

        //   Man (Absen) rows                       ─
        $absenQuery = \App\Models\AbsenceRecord::where('sc_id', $scId)->where('factory', $factory)->where('status', 'absen');
        $replQuery = \App\Models\AssignmentReplacement::where('sc_id', $scId)->where('factory', $factory)->with('member');

        if ($isTvMode) {
            // For TV mode, we need open absences (today only? or any day? absences usually reset daily)
            // But History should show past month's absences!
            $absenQuery->where('tanggal', '>=', $startDate);
            $replQuery->where('tanggal', '>=', $startDate);
        } else {
            $absenQuery->where(['tanggal' => $request->tanggal, 'shift' => $request->shift]);
            $replQuery->where(['tanggal' => $request->tanggal, 'shift' => $request->shift]);
        }

        $absenRecords = $absenQuery->get();
        $replacementsData = $replQuery->get();

        // Map: tanggal_shift_target_machine => [replacement member names]
        $replacementMap = [];
        foreach ($replacementsData as $repl) {
            $tReplStr = $repl->tanggal instanceof \Carbon\Carbon ? $repl->tanggal->toDateString() : substr((string) $repl->tanggal, 0, 10);
            $key = $isTvMode ? "{$tReplStr}_{$repl->shift}_{$repl->target_machine}" : $repl->target_machine;
            $name = $repl->member?->nama ?? 'Pengganti';
            $replacementMap[$key][] = $name;
        }

        $absenRows = collect();
        foreach ($absenRecords as $record) {
            $member = \App\Models\Member::where('sc_id', $scId)->find($record->member_id);
            if (!$member)
                continue;

            $reason = strtolower($record->reason ?? '');
            $absenLabel = match (true) {
                str_contains($reason, 'sakit') => 'SAKIT',
                str_contains($reason, 'izin') || str_contains($reason, 'ijin') => 'IZIN',
                str_contains($reason, 'cuti') => 'CUTI',
                default => 'ABSEN',
            };

            $mesinArr = array_filter([trim((string) ($member->mesin ?? '')), trim((string) ($member->mesin_secondary ?? ''))]);
            $mesinList = empty($mesinArr) ? ['-'] : $mesinArr;

            foreach ($mesinList as $mesin) {
                $tAbsStr = $record->tanggal instanceof \Carbon\Carbon ? $record->tanggal->toDateString() : substr((string) $record->tanggal, 0, 10);
                $key = $isTvMode ? "{$tAbsStr}_{$record->shift}_{$mesin}" : $mesin;
                $backupNames = $replacementMap[$key] ?? [];
                $hasReplacement = !empty($backupNames);
                $backupLabel = $hasReplacement ? implode(', ', $backupNames) : null;
                $status = 'open';
                if ($isTvMode) {
                    if ($tAbsStr !== $request->tanggal || $record->shift !== $request->shift) {
                        $status = 'closed';
                    }
                } elseif ($hasReplacement) {
                    $status = 'closed';
                }

                $absenRows->push([
                    'id' => 'absen-' . $record->id . '-' . $mesin,
                    'jenis' => 'Man',
                    'lokasi' => $mesin,
                    'tanggal' => $tAbsStr,
                    'waktu_mulai' => null,
                    'waktu_selesai' => null,
                    'durasi' => null,
                    'deskripsi' => "{$member->nama}  - {$absenLabel}",
                    'status' => $status,
                    'cause' => $absenLabel,
                    'countermeasure' => $hasReplacement ? "Backup: {$backupLabel}" : null,
                    'pic' => null,
                    '_source' => 'absen',
                    '_backup_name' => $backupLabel,
                    '_has_replacement' => $hasReplacement,
                ]);
            }
        }

        $combined = $logs->concat($absenRows)
            ->sortByDesc(function ($r) {
                return $r['status'] === 'open' ? '9999-99-99' : $r['tanggal'] . ' ' . ($r['waktu_mulai'] ?? '00:00');
            })
            ->values();

        return response()->json($combined);
    }

    /**
     * Hitung durasi antara dua waktu HH:mm.
     * Handle lintas tengah malam (misal: 23:00 – 01:30 = 2j 30m).
     */
    private function calcDuration(string $start, string $end): string
    {
        $start = substr(trim($start), 0, 5);
        $end = substr(trim($end), 0, 5);

        $s = strtotime("2000-01-01 {$start}");
        $e = strtotime("2000-01-01 {$end}");
        $diff = $e - $s;

        if ($diff < 0)
            $diff += 86400;

        $h = intdiv($diff, 3600);
        $m = intdiv($diff % 3600, 60);

        if ($h > 0 && $m > 0)
            return "{$h}j {$m}m";
        if ($h > 0)
            return "{$h}j";
        return "{$m}m";
    }
}
