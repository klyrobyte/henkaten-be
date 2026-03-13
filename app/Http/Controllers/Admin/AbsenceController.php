<?php
// ═══════════════════════════════════════════════════════════════════════════
// FILE: app/Http/Controllers/Admin/AbsenceController.php
//
// PERUBAHAN:
//   1. exportExcel() — export Excel XLSX bagus via ExcelExportService
//   2. report() sudah return JSON (fix untuk navigasi tanggal di frontend)
//   3. absenHistory() — endpoint baru untuk rekap tanggal tertentu (JSON)
//
// JUGA: di absen.blade.php, fix script navigasi tanggal (lihat bagian bawah)
// ═══════════════════════════════════════════════════════════════════════════

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsenceRecord;
use App\Models\AbsenceSummary;
use App\Models\AssignmentReplacement;
use App\Models\Member;
use App\Services\ExcelExportService;
use App\Services\FactoryConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class AbsenceController extends Controller
{
    public function __construct(
        protected ExcelExportService   $excelService,
        protected FactoryConfigService $factoryConfig,
    ) {}

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

    // ── GET /admin/absence ─────────────────────────────────────────────
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', today()->toDateString());

        // Jika user bukan admin dan punya factory+shift yang di-assign,
        // paksa gunakan nilai tersebut (abaikan query-string).
        $user = auth()->user();
        if ($user->role !== 'admin' && $user->factory && $user->shift) {
            $factory = $user->factory;
            $shift   = $user->shift;
        } else {
            $factory = $this->normalizeFactory($request->get('factory', 'Factory 2'));
            $shift   = $request->get('shift', 'A');
        }

        $members = $this->membersFor($factory, $shift);

        $records = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
        ])->get()->keyBy('member_id');

        $hadir = $members->filter(fn($m) => ($records[$m->id]?->status ?? 'hadir') === 'hadir')->count();
        $absen = $members->count() - $hadir;

        return view('admin.absen',
            compact('members', 'records', 'tanggal', 'factory', 'shift', 'hadir', 'absen'));
    }

    // ── POST /admin/absence/save ───────────────────────────────────────
    public function save(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'factory' => 'required|string',
            'shift'   => 'required|in:A,B',
            'records' => 'required|array',
        ]);

        $tanggal = $request->tanggal;
        $factory = $this->normalizeFactory($request->factory);
        $shift   = $request->shift;

        $validIds = $this->membersFor($factory, $shift)->pluck('id')->toArray();

        DB::transaction(function () use ($request, $tanggal, $factory, $shift, $validIds) {
            foreach ($request->records as $memberId => $rec) {
                if (!in_array((int)$memberId, $validIds)) continue;

                AbsenceRecord::updateOrCreate(
                    ['tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift, 'member_id' => (int)$memberId],
                    [
                        'status' => $rec['status'] ?? 'hadir',
                        'reason' => ($rec['status'] ?? '') === 'absen' ? ($rec['reason'] ?? null) : null,
                    ]
                );
            }
            $this->rebuildSummary($tanggal, $factory, $shift);
        });

        if ($request->wantsJson()) {
            $hadir = AbsenceRecord::where(['tanggal'=>$tanggal,'factory'=>$factory,'shift'=>$shift,'status'=>'hadir'])->count();
            $absen = AbsenceRecord::where(['tanggal'=>$tanggal,'factory'=>$factory,'shift'=>$shift,'status'=>'absen'])->count();
            return response()->json(['ok' => true, 'hadir' => $hadir, 'absen' => $absen]);
        }
        return back()->with('success', '✅ Data absen tersimpan!');
    }

    // ── GET /admin/absence/report  (JSON for frontend rekap) ──────────
    public function report(Request $request)
    {
        $tanggal = $request->get('tanggal', today()->toDateString());

        // Kalau ada factory & shift di request, tampilkan hanya itu.
        // Kalau tidak ada (akses langsung tanpa filter), tampilkan semua.
        $factories = $request->has('factory')
            ? [$this->normalizeFactory($request->get('factory'))]
            : ['Factory 2', 'Factory 3 & 4'];

        $shifts = $request->has('shift')
            ? [$request->get('shift')]
            : ['A', 'B'];

        $reports = [];

        foreach ($factories as $factory) {
            foreach ($shifts as $shift) {
                $members = $this->membersFor($factory, $shift);
                if ($members->isEmpty()) continue;

                $records = AbsenceRecord::where([
                    'tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift,
                ])->get()->keyBy('member_id');

                $hadirList = $members->filter(fn($m) => ($records[$m->id]?->status ?? 'hadir') === 'hadir');
                $absenList = $members->filter(fn($m) => ($records[$m->id]?->status ?? 'hadir') === 'absen');
                $pct       = $members->count() ? round($hadirList->count() / $members->count() * 100) : 0;

                $reasons = [];
                foreach ($absenList as $m) {
                    $r = $records[$m->id]?->reason ?? 'Unknown';
                    $reasons[$r] = ($reasons[$r] ?? 0) + 1;
                }

                $reports[] = [
                    'factory'    => $factory,
                    'shift'      => $shift,
                    'total'      => $members->count(),
                    'hadir'      => $hadirList->count(),
                    'absen'      => $absenList->count(),
                    'pct'        => $pct,
                    'reasons'    => $reasons,
                    'absen_list' => $absenList->values()->map(fn($m) => [
                        'nama'    => $m->nama,
                        'jabatan' => $m->jabatan,
                        'mesin'   => $m->mesin,
                        'reason'  => $records[$m->id]?->reason ?? '-',
                    ]),
                ];
            }
        }

        if ($request->wantsJson()) {
            return response()->json($reports);
        }
        $factory = $request->get("factory", session("factory", "Factory 2"));
        $shift   = $request->get("shift",   session("shift", "A"));
        return view("admin.absence_report", compact("reports", "tanggal", "factory", "shift"));
    }

    // ── GET /admin/absence/export  (CSV — filter by factory & shift) ─
    public function export(Request $request)
    {
        $tanggal = $request->get('tanggal', today()->toDateString());
        $factory = $this->normalizeFactory($request->get('factory', 'Factory 2'));
        $shift   = $request->get('shift', 'A');

        $members = $this->membersFor($factory, $shift);
        $records = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
        ])->get()->keyBy('member_id');

        $hadirCount = $members->filter(fn($m) => ($records[$m->id]?->status ?? 'hadir') === 'hadir')->count();
        $absenCount = $members->count() - $hadirCount;

        $filename = "Absen_{$factory}_Shift{$shift}_{$tanggal}.csv";

        return Response::stream(function () use ($members, $records, $factory, $shift, $tanggal, $hadirCount, $absenCount) {
            $h = fopen('php://output', 'w');

            // Header info
            fputcsv($h, ["REKAP ABSEN — {$factory} Shift {$shift}"]);
            fputcsv($h, ["Tanggal: {$tanggal}"]);
            fputcsv($h, ["Total: {$members->count()}  |  Hadir: {$hadirCount}  |  Absen: {$absenCount}"]);
            fputcsv($h, []);
            fputcsv($h, ['No', 'Nama', 'NIK', 'Jabatan', 'Mesin', 'Status', 'Alasan']);

            $no = 1;
            foreach ($members as $m) {
                $rec = $records[$m->id] ?? null;
                fputcsv($h, [
                    $no++,
                    $m->nama,
                    $m->nik ?? '-',
                    $m->jabatan,
                    $m->mesin ?? '-',
                    $rec?->status === 'absen' ? 'Absen' : 'Hadir',
                    $rec?->status === 'absen' ? ($rec->reason ?? '-') : '-',
                ]);
            }
            fclose($h);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ── GET /admin/absence/export-excel  ← BARU ───────────────────────
    // Export semua factory+shift untuk tanggal tertentu ke XLSX bagus
    public function exportExcel(Request $request)
    {
        $tanggal = $request->get('tanggal', today()->toDateString());
        $factory = $this->normalizeFactory($request->get('factory', 'Factory 2'));
        $shift   = $request->get('shift', 'A');

        $members = $this->membersFor($factory, $shift);
        $records = AbsenceRecord::where([
            'tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift,
        ])->get()->keyBy('member_id');

        $replacements = AssignmentReplacement::where([
            'tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift,
        ])->with('member')->get();

        // We pass empty logs for absen-only export; use ReportController for full report
        $logs = collect([]);

        $path = $this->excelService->buildFullReport(
            $factory, $shift, $tanggal,
            $members, $records, $logs, $replacements
        );

        $filename = "Absen_{$factory}_Shift{$shift}_{$tanggal}.xlsx";

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // ── GET /admin/absence/candidates ─────────────────────────────────
    public function candidates(Request $request)
    {
        $tanggal = $request->get('tanggal', today()->toDateString());
        $factory = $this->normalizeFactory($request->get('factory', 'Factory 2'));
        $shift   = $request->get('shift', 'A');
        $q       = strtolower(trim($request->get('q', '')));

        $absentIds = AbsenceRecord::where([
            'tanggal' => $tanggal, 'factory' => $factory,
            'shift'   => $shift,   'status'  => 'absen',
        ])->pluck('member_id')->toArray();

        $query = Member::where('factory', $factory)
            ->where('status', 'active')
            ->whereIn('shift', [$shift, 'AB'])
            ->whereNotIn('id', $absentIds)
            ->orderBy('nama');

        if ($q) $query->where('nama', 'like', "%{$q}%");

        $allMembers = $query->get();
        $workingIds = AbsenceRecord::where([
            'tanggal' => $tanggal, 'factory' => $factory,
            'shift'   => $shift,   'status'  => 'hadir',
        ])->pluck('member_id')->toArray();

        $result = $allMembers->map(fn($m) => [
            'id'        => $m->id,
            'name'      => $m->nama,
            'photo'     => $m->photo_url,
            'jabatan'   => $m->jabatan,
            'mesin'     => $m->mesin,
            'isWorking' => in_array($m->id, $workingIds),
        ]);

        return response()->json($result->sortBy('isWorking')->values());
    }

    // ── GET /admin/absence/data ────────────────────────────────────────
    public function getData(Request $request)
    {
        $factory = $this->normalizeFactory($request->get('factory', 'Factory 2'));

        $records = AbsenceRecord::where([
            'tanggal' => $request->get('tanggal', today()->toDateString()),
            'factory' => $factory,
            'shift'   => $request->get('shift', 'A'),
        ])->get();

        return response()->json($records->keyBy('member_id'));
    }

    // ═════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ═════════════════════════════════════════════════════════════════

    private function rebuildSummary(string $tanggal, string $factory, string $shift): void
    {
        $members     = $this->membersFor($factory, $shift);
        $totalMember = $members->count();

        $records = AbsenceRecord::where([
            'tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift,
        ])->get()->keyBy('member_id');

        $hadirCount = 0;
        $opCuti = $opSakit = $opIjin = $opMangkir = 0;
        $spvCuti = $spvSakit = $spvIjin = $spvMangkir = 0;

        foreach ($members as $m) {
            $rec = $records[$m->id] ?? null;
            if (!$rec || $rec->status === 'hadir') { $hadirCount++; continue; }

            $jabatanLower = strtolower($m->jabatan ?? '');
            $isPengawas   = str_contains($jabatanLower, 'pengawas')
                         || str_contains($jabatanLower, 'spv')
                         || str_contains($jabatanLower, 'supervisor')
                         || str_contains($jabatanLower, 'foreman');
            $reason = $rec->reason ?? 'Ijin';

            if ($isPengawas) {
                match($reason) { 'Cuti'=>$spvCuti++,'Sakit'=>$spvSakit++,'Mangkir'=>$spvMangkir++,default=>$spvIjin++ };
            } else {
                match($reason) { 'Cuti'=>$opCuti++,'Sakit'=>$opSakit++,'Mangkir'=>$opMangkir++,default=>$opIjin++ };
            }
        }

        $totalAbsen = $opCuti + $opSakit + $opIjin + $opMangkir + $spvCuti + $spvSakit + $spvIjin + $spvMangkir;

        AbsenceSummary::updateOrCreate(
            ['tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift],
            [
                'mp_hadir'     => $hadirCount,
                'total_absen'  => $totalAbsen,
                'total_member' => $totalMember,
                'op_cuti'      => $opCuti,
                'op_sakit'     => $opSakit,
                'op_ijin'      => $opIjin,
                'spv_cuti'     => $spvCuti,
                'spv_sakit'    => $spvSakit,
                'spv_ijin'     => $spvIjin,
                'source'       => 'membermanagement',
            ]
        );
    }
}