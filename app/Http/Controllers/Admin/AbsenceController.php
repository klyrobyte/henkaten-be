<?php
// ═══════════════════════════════════════════════════════════════════════════
// FILE: app/Http/Controllers/Admin/AbsenceController.php
// BY: Rizky Daffy
// PERUBAHAN:
//   1. exportExcel()  - export Excel XLSX bagus via ExcelExportService
//   2. report() sudah return JSON (fix untuk navigasi tanggal di frontend)
//   3. absenHistory()  - endpoint baru untuk rekap tanggal tertentu (JSON)
//
// JUGA: di absen.blade.php, fix script navigasi tanggal (lihat bagian bawah)
// ═══════════════════════════════════════════════════════════════════════════

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsenceRecord;
use App\Models\AbsenceSummary;
use App\Models\AssignmentReplacement;
use App\Models\Member;
use App\Models\Factory;
use App\Services\ExcelExportService;
use App\Services\FactoryConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

/**
 * @group Absence
 * 
 * APIs for managing Absence.
 */
class AbsenceController extends Controller
{
    public function __construct(
        protected ExcelExportService $excelService,
        protected FactoryConfigService $factoryConfig,
    ) {
    }

    private function normalizeFactory(?string $factory): string
    {
        return html_entity_decode($factory ?? 'Factory 2', ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function membersFor(string $factory, string $shift)
    {
        return Member::where('factory', $factory)
            ->whereIn('shift', [$shift, 'AB'])
            ->where('status', 'active')
            ->orderBy('nama')
            ->get();
    }

    // @rizky ── GET /admin/absence ─────────────────────────────────────────────
    public function index(Request $request)
    {
        $user = Auth::user();
        $userFactories = (array) $user->factory;

        if ($user->isSuperAdmin()) {
            $factories = Factory::all();
        } else {
            $factories = Factory::whereIn('name', $userFactories)->get();
        }

        $tanggal = $request->get('tanggal', today()->toDateString());
        $factory = $this->normalizeFactory($request->get('factory'));

        if (!$user->isSuperAdmin() && !in_array($factory, $userFactories)) {
            $factory = $userFactories[0] ?? 'Factory 2';
        }

        $shift = $request->get('shift', 'A');

        $members = $this->membersFor($factory, $shift);

        $records = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
        ])->get()->keyBy('member_id');

        $hadir = $members->filter(fn($m) => ($records[$m->id]?->status ?? 'hadir') === 'hadir')->count();
        $absen = $members->count() - $hadir;

        return view(
            'admin.absen',
            compact('members', 'records', 'tanggal', 'factory', 'shift', 'hadir', 'absen', 'factories')
        );
    }

    // @rizky ── POST /admin/absence/save ───────────────────────────────────────
    public function save(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'factory' => 'required|string',
            'shift' => 'required|in:A,B',
            'records' => 'required|array',
        ]);

        $tanggal = $request->tanggal;
        $factory = $this->normalizeFactory($request->factory);

        $user = Auth::user();
        $userFactories = (array) $user->factory;
        if (!$user->isSuperAdmin() && !in_array($factory, $userFactories)) {
            $factory = $userFactories[0] ?? 'Factory 2';
        }

        $shift = $request->shift;

        $validIds = $this->membersFor($factory, $shift)->pluck('id')->toArray();

        DB::transaction(function () use ($request, $tanggal, $factory, $shift, $validIds) {
            foreach ($request->records as $memberId => $rec) {
                if (!in_array((int) $memberId, $validIds))
                    continue;

                AbsenceRecord::updateOrCreate(
                    ['tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift, 'member_id' => (int) $memberId],
                    [
                        'status' => $rec['status'] ?? 'hadir',
                        'reason' => ($rec['status'] ?? '') === 'absen' ? ($rec['reason'] ?? null) : null,
                    ]
                );
            }
            $this->rebuildSummary($tanggal, $factory, $shift);
        });

        if ($request->wantsJson()) {
            $hadir = AbsenceRecord::where(['tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift, 'status' => 'hadir'])->count();
            $absen = AbsenceRecord::where(['tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift, 'status' => 'absen'])->count();
            return response()->json(['ok' => true, 'hadir' => $hadir, 'absen' => $absen]);
        }
        return back()->with('success', '✅ Data absen tersimpan!');
    }

    // @rizky ── GET /admin/absence/report  (JSON for frontend rekap) ──────────
    public function report(Request $request)
    {
        $user = Auth::user();
        $userFactories = (array) $user->factory;
        $tanggal = $request->get('tanggal', today()->toDateString());

        // Kalau ada factory & shift di request, tampilkan hanya itu.
        // Kalau tidak ada (akses langsung tanpa filter), tampilkan semua.
        if ($request->has('factory')) {
            $factory = $this->normalizeFactory($request->get('factory'));
            if (!$user->isSuperAdmin() && !in_array($factory, $userFactories)) {
                $factory = $userFactories[0] ?? 'Factory 2';
            }
            $factories = [$factory];
        } else {
            $factories = $user->isSuperAdmin() ? ['Factory 2', 'Factory 3 & 4'] : $userFactories;
        }

        $shifts = $request->has('shift')
            ? [$request->get('shift')]
            : ['A', 'B'];

        $reports = [];

        foreach ($factories as $factory) {
            foreach ($shifts as $shift) {
                $members = $this->membersFor($factory, $shift);
                if ($members->isEmpty())
                    continue;

                $records = AbsenceRecord::where([
                    'tanggal' => $tanggal,
                    'factory' => $factory,
                    'shift' => $shift,
                ])->get()->keyBy('member_id');

                $hadirList = $members->filter(fn($m) => ($records[$m->id]?->status ?? 'hadir') === 'hadir');
                $absenList = $members->filter(fn($m) => ($records[$m->id]?->status ?? 'hadir') === 'absen');
                $pct = $members->count() ? round($hadirList->count() / $members->count() * 100) : 0;

                $reasons = [];
                foreach ($absenList as $m) {
                    $r = $records[$m->id]?->reason ?? 'Unknown';
                    $reasons[$r] = ($reasons[$r] ?? 0) + 1;
                }

                $reports[] = [
                    'factory' => $factory,
                    'shift' => $shift,
                    'total' => $members->count(),
                    'hadir' => $hadirList->count(),
                    'absen' => $absenList->count(),
                    'pct' => $pct,
                    'reasons' => $reasons,
                    'absen_list' => $absenList->values()->map(fn($m) => [
                        'nama' => $m->nama,
                        'jabatan' => $m->jabatan,
                        'mesin' => $m->mesin,
                        'reason' => $records[$m->id]?->reason ?? '-',
                    ]),
                ];
            }
        }

        if ($request->wantsJson()) {
            return response()->json($reports);
        }
        $factory = $request->get("factory", session("factory", "Factory 2"));
        $shift = $request->get("shift", session("shift", "A"));
        return view("admin.absence_report", compact("reports", "tanggal", "factory", "shift"));
    }

    // @rizky ── GET /admin/absence/export  (CSV  - filter by factory & shift) ─
    // Task 2: Adds RINGKASAN KEHADIRAN summary at end of CSV
    // Task 3: Adds Pengganti column for each absent member
    public function export(Request $request)
    {
        $user = Auth::user();
        $userFactories = (array) $user->factory;
        $tanggal = $request->get('tanggal', today()->toDateString());
        $factory = $this->normalizeFactory($request->get('factory', 'Factory 2'));

        if (!$user->isSuperAdmin() && !in_array($factory, $userFactories)) {
            $factory = $userFactories[0] ?? 'Factory 2';
        }

        $shift = $request->get('shift', 'A');

        $members = $this->membersFor($factory, $shift);
        $records = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
        ])->get()->keyBy('member_id');

        // Task 3: fetch pengganti (replacement) keyed by the ABSENT member's original mesin
        $replacementsRaw = AssignmentReplacement::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
        ])->with('member')->get();

        // Build a map: absent_member_mesin → pengganti_name(s)
        // AssignmentReplacement.source_machine = original machine of absent KY person
        $penggantiByMesin = [];
        foreach ($replacementsRaw as $repl) {
            $srcMesin = $repl->source_machine ?? null;
            if ($srcMesin) {
                $penggantiByMesin[$srcMesin][] = $repl->member?->nama ?? '-';
            }
        }

        $hadirCount = $members->filter(fn($m) => ($records[$m->id]?->status ?? 'hadir') === 'hadir')->count();
        $absenCount = $members->count() - $hadirCount;

        // Task 2: compute KY absent count and status level for summary
        $absenMemberIds = $records->where('status', 'absen')->pluck('member_id')->toArray();
        $kyAbsenCount = $members->whereIn('id', $absenMemberIds)->filter(function ($m) {
            return str_starts_with(strtolower($m->mesin ?? ''), 'ky');
        })->count();
        $activeMC = \App\Models\ProblemLog::where('factory', $factory)
            ->where('shift', $shift)
            ->where('status', 'open')
            ->whereIn('jenis', ['Machine', 'Material', 'Method'])
            ->count();
        $statusLabels = ['NORMAL', 'PERHATIAN RINGAN', 'PERHATIAN KHUSUS', 'BAHAYA'];
        $statusIndex = 0;
        if ($kyAbsenCount === 0 && $activeMC === 0)
            $statusIndex = 0;
        elseif ($kyAbsenCount > 0 && $activeMC === 0)
            $statusIndex = 1;
        elseif ($kyAbsenCount > 0 && $activeMC > 0 && $activeMC < 2)
            $statusIndex = 2;
        elseif ($kyAbsenCount > 0 && $activeMC > 2)
            $statusIndex = 3;
        elseif ($activeMC > 0)
            $statusIndex = 2;
        $statusLabel = $statusLabels[$statusIndex];

        $filename = "Absen_{$factory}_Shift{$shift}_{$tanggal}.csv";

        return Response::stream(function () use ($members, $records, $factory, $shift, $tanggal, $hadirCount, $absenCount, $kyAbsenCount, $activeMC, $statusLabel, $penggantiByMesin) {
            $h = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fputs($h, "\xEF\xBB\xBF");

            // Header info
            fputcsv($h, ["REKAP ABSEN  - {$factory} Shift {$shift}"]);
            fputcsv($h, ["Tanggal: {$tanggal}"]);
            fputcsv($h, ["Total: {$members->count()}  |  Hadir: {$hadirCount}  |  Absen: {$absenCount}"]);
            fputcsv($h, []);
            fputcsv($h, ['No', 'Tanggal', 'Nama Lengkap', 'NIK', 'Jabatan', 'Shift', 'Factory', 'Mesin', 'Status', 'Alasan', 'Pengganti', 'Catatan']);

            $no = 1;
            foreach ($members as $m) {
                $rec = $records[$m->id] ?? null;
                $isAbsen = $rec?->status === 'absen';
                $mesinLower = strtolower($m->mesin ?? '');
                $isKeyPerson = str_starts_with($mesinLower, 'gl')
                    || str_starts_with($mesinLower, 'tl')
                    || str_starts_with($mesinLower, 'ky');
                $jabatanDisplay = $isKeyPerson ? 'Pengawas' : ($m->jabatan ?? '-');

                // Task 3: resolve pengganti name for this absent member
                $penggantiDisplay = '-';
                if ($isAbsen) {
                    $names = $penggantiByMesin[$m->mesin ?? ''] ?? [];
                    $penggantiDisplay = !empty($names) ? 'Digantikan oleh ' . implode(' & ', $names) : '-';
                }

                fputcsv($h, [
                    $no++,
                    $tanggal,
                    $m->nama,
                    $m->nik ?? '-',
                    $jabatanDisplay,
                    $shift,
                    $factory,
                    $m->mesin ?? '-',
                    $isAbsen ? 'Absen' : 'Hadir',
                    $isAbsen ? ($rec->reason ?? '-') : '-',
                    $penggantiDisplay,
                    '', // Catatan
                ]);
            }

            // Task 2: RINGKASAN KEHADIRAN block at bottom of CSV
            fputcsv($h, []);
            fputcsv($h, []);
            fputcsv($h, ['===== RINGKASAN KEHADIRAN =====']);
            fputcsv($h, ['Tanggal Export', now()->format('d/m/Y H:i')]);
            fputcsv($h, ['Periode', $tanggal]);
            fputcsv($h, ['Factory', $factory]);
            fputcsv($h, ['Shift', $shift]);
            fputcsv($h, ['Total Member', $members->count()]);
            fputcsv($h, ['Total Hadir', $hadirCount]);
            fputcsv($h, ['Total Absen', $absenCount]);
            fputcsv($h, ['Total KY Absen', $kyAbsenCount]);
            fputcsv($h, ['Total MC Problem (open)', $activeMC]);
            fputcsv($h, ['Status Keseluruhan', $statusLabel]);

            fclose($h);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // @rizky ── GET /admin/absence/export-excel  ← BARU ───────────────────────
    // Export semua factory+shift untuk tanggal tertentu ke XLSX bagus
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $userFactories = (array) $user->factory;
        $tanggal = $request->get('tanggal', today()->toDateString());
        $factory = $this->normalizeFactory($request->get('factory', 'Factory 2'));

        if (!$user->isSuperAdmin() && !in_array($factory, $userFactories)) {
            $factory = $userFactories[0] ?? 'Factory 2';
        }

        $shift = $request->get('shift', 'A');

        $members = $this->membersFor($factory, $shift);
        $records = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
        ])->get()->keyBy('member_id');

        $replacements = AssignmentReplacement::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
        ])->with('member')->get();

        // @dave: We pass empty logs for absen-only export; use ReportController for full report
        $logs = collect([]);

        $path = $this->excelService->buildFullReport(
            $factory,
            $shift,
            $tanggal,
            $members,
            $records,
            $logs,
            $replacements
        );

        $filename = "Absen_{$factory}_Shift{$shift}_{$tanggal}.xlsx";

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // @rizky ── GET /admin/absence/candidates ─────────────────────────────────
    public function candidates(Request $request)
    {
        $user = Auth::user();
        $userFactories = (array) $user->factory;
        $tanggal = $request->get('tanggal', today()->toDateString());
        $factory = $this->normalizeFactory($request->get('factory', 'Factory 2'));

        if (!$user->isSuperAdmin() && !in_array($factory, $userFactories)) {
            $factory = $userFactories[0] ?? 'Factory 2';
        }

        $shift = $request->get('shift', 'A');
        $q = strtolower(trim($request->get('q', '')));

        $absentIds = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'absen',
        ])->pluck('member_id')->toArray();

        $query = Member::where('factory', $factory)
            ->where('status', 'active')
            ->whereIn('shift', [$shift, 'AB'])
            ->whereNotIn('id', $absentIds)
            ->orderBy('nama');

        if ($q)
            $query->where('nama', 'like', "%{$q}%");

        $allMembers = $query->get();
        $workingIds = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'hadir',
        ])->pluck('member_id')->toArray();

        $result = $allMembers->map(fn($m) => [
            'id' => $m->id,
            'name' => $m->nama,
            'photo' => $m->photo_url,
            'jabatan' => $m->jabatan,
            'mesin' => $m->mesin,
            'isWorking' => in_array($m->id, $workingIds),
        ]);

        return response()->json($result->sortBy('isWorking')->values());
    }

    // @rizky ── GET /admin/absence/data ────────────────────────────────────────
    public function getData(Request $request)
    {
        $user = Auth::user();
        $userFactories = (array) $user->factory;
        $factory = $this->normalizeFactory($request->get('factory', 'Factory 2'));

        if (!$user->isSuperAdmin() && !in_array($factory, $userFactories)) {
            $factory = $userFactories[0] ?? 'Factory 2';
        }

        $records = AbsenceRecord::where([
            'tanggal' => $request->get('tanggal', today()->toDateString()),
            'factory' => $factory,
            'shift' => $request->get('shift', 'A'),
        ])->get();

        return response()->json($records->keyBy('member_id'));
    }

    // @rizky ── POST /admin/absence/rebuild-summary  (perbaiki data summary yg tersimpan) ──
    public function rebuildSummaryEndpoint(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'factory' => 'required|string',
            'shift' => 'required|in:A,B',
        ]);
        $tanggal = $request->tanggal;
        $factory = $this->normalizeFactory($request->factory);

        $user = Auth::user();
        $userFactories = (array) $user->factory;
        if (!$user->isSuperAdmin() && !in_array($factory, $userFactories)) {
            $factory = $userFactories[0] ?? 'Factory 2';
        }

        $shift = $request->shift;
        $this->rebuildSummary($tanggal, $factory, $shift);
        return response()->json(['ok' => true, 'message' => 'Summary berhasil direbuild']);
    }

    // ═════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ═════════════════════════════════════════════════════════════════

    public function rebuildSummary(string $tanggal, string $factory, string $shift): void
    {
        $members = $this->membersFor($factory, $shift);
        $totalMember = $members->count();

        $records = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
        ])->get()->keyBy('member_id');

        $hadirCount = 0;
        $opCuti = $opSakit = $opIjin = $opAlpha = 0;
        $spvCuti = $spvSakit = $spvIjin = $spvAlpha = 0;

        foreach ($members as $m) {
            $rec = $records[$m->id] ?? null;
            if (!$rec || $rec->status === 'hadir') {
                $hadirCount++;
                continue;
            }

            // ── Deteksi Key Person / Pengawas ──────────────────────────────
            // Sumber primer: field `mesin`  - Key Persons di FactoryConfigService
            //   punya nilai mesin seperti 'GL', 'TL Resin', 'KY Assy (1)', dll.
            // Sumber sekunder: field `jabatan`  - untuk Factory 3&4 yang sudah benar (GL, TL, KY).
            // Factory 2 Key Persons ber-jabatan 'Operator', jadi tidak bisa diandalkan jabatan.
            $mesinLower = strtolower($m->mesin ?? '');
            $jabatanLower = strtolower($m->jabatan ?? '');
            $isPengawas = str_starts_with($mesinLower, 'gl')
                || str_starts_with($mesinLower, 'tl')
                || str_starts_with($mesinLower, 'ky')
                || in_array($jabatanLower, ['gl', 'tl', 'ky', 'spv', 'supervisor', 'foreman', 'pengawas']);
            $reason = $rec->reason ?? 'Ijin';

            if ($isPengawas) {
                match ($reason) { 'Cuti' => $spvCuti++, 'Sakit' => $spvSakit++, 'Alpha' => $spvAlpha++, default => $spvIjin++};
            } else {
                match ($reason) { 'Cuti' => $opCuti++, 'Sakit' => $opSakit++, 'Alpha' => $opAlpha++, default => $opIjin++};
            }
        }

        $totalAbsen = $opCuti + $opSakit + $opIjin + $opAlpha + $spvCuti + $spvSakit + $spvIjin + $spvAlpha;

        AbsenceSummary::updateOrCreate(
            ['tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift],
            [
                'mp_hadir' => $hadirCount,
                'total_absen' => $totalAbsen,
                'total_member' => $totalMember,
                'op_cuti' => $opCuti,
                'op_sakit' => $opSakit,
                'op_ijin' => $opIjin,
                'spv_cuti' => $spvCuti,
                'spv_sakit' => $spvSakit,
                'spv_ijin' => $spvIjin,
                'source' => 'membermanagement',
            ]
        );
    }
}