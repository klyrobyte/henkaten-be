<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsenceRecord;
use App\Models\AbsenceSummary;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class AbsenceController extends Controller
{
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

    // ──────────────────────────────────────────────────────────────────
    // HALAMAN INPUT ABSEN
    // GET /admin/absence
    // ✅ FIX: view dipindah dari 'admin.member.absen' → 'admin.absen'
    // ──────────────────────────────────────────────────────────────────
    public function index(Request $request)
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

        $hadir = $members->filter(fn($m) => ($records[$m->id]?->status ?? 'hadir') === 'hadir')->count();
        $absen = $members->count() - $hadir;

        // ✅ view yang benar: admin.absen (bukan admin.member.absen)
        return view('admin.absen',
            compact('members', 'records', 'tanggal', 'factory', 'shift', 'hadir', 'absen'));
    }

    // ──────────────────────────────────────────────────────────────────
    // SIMPAN ABSEN BATCH
    // POST /admin/absence/save
    // ──────────────────────────────────────────────────────────────────
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
                    [
                        'tanggal'   => $tanggal,
                        'factory'   => $factory,
                        'shift'     => $shift,
                        'member_id' => (int)$memberId,
                    ],
                    [
                        'status' => $rec['status'] ?? 'hadir',
                        'reason' => ($rec['status'] ?? '') === 'absen'
                            ? ($rec['reason'] ?? null)
                            : null,
                    ]
                );
            }

            $this->rebuildSummary($tanggal, $factory, $shift);
        });

        if ($request->wantsJson()) {
            $hadir = AbsenceRecord::where([
                'tanggal' => $tanggal, 'factory' => $factory,
                'shift'   => $shift,   'status'  => 'hadir',
            ])->count();
            $absen = AbsenceRecord::where([
                'tanggal' => $tanggal, 'factory' => $factory,
                'shift'   => $shift,   'status'  => 'absen',
            ])->count();
            return response()->json(['ok' => true, 'hadir' => $hadir, 'absen' => $absen]);
        }

        return back()->with('success', '✅ Data absen tersimpan!');
    }

    // ──────────────────────────────────────────────────────────────────
    // KANDIDAT PENGGANTI
    // GET /admin/absence/candidates
    // ──────────────────────────────────────────────────────────────────
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

        if ($q) {
            $query->where('nama', 'like', "%{$q}%");
        }

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

    // ──────────────────────────────────────────────────────────────────
    // DATA ABSEN JSON
    // GET /admin/absence/data
    // ──────────────────────────────────────────────────────────────────
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

    // ──────────────────────────────────────────────────────────────────
    // HALAMAN REKAP
    // GET /admin/absence/report
    // ──────────────────────────────────────────────────────────────────
    public function report(Request $request)
    {
        $tanggal = $request->get('tanggal', today()->toDateString());
        $reports = [];

        foreach (['Factory 2', 'Factory 3 & 4'] as $factory) {
            foreach (['A', 'B'] as $shift) {
                $members = $this->membersFor($factory, $shift);
                if ($members->isEmpty()) continue;

                $records = AbsenceRecord::where([
                    'tanggal' => $tanggal,
                    'factory' => $factory,
                    'shift'   => $shift,
                ])->get()->keyBy('member_id');

                $hadirList = $members->filter(fn($m) => ($records[$m->id]?->status ?? 'hadir') === 'hadir');
                $absenList = $members->filter(fn($m) => ($records[$m->id]?->status ?? 'hadir') === 'absen');
                $pct       = $members->count() ? round($hadirList->count() / $members->count() * 100) : 0;

                $reasons = [];
                foreach ($absenList as $m) {
                    $r = $records[$m->id]?->reason ?? 'Unknown';
                    $reasons[$r] = ($reasons[$r] ?? 0) + 1;
                }

                $reports[] = compact('factory', 'shift', 'members', 'records', 'hadirList', 'absenList', 'pct', 'reasons');
            }
        }

        return view('admin.member.report', compact('reports', 'tanggal'));
    }

    // ──────────────────────────────────────────────────────────────────
    // EXPORT CSV
    // GET /admin/absence/export
    // ──────────────────────────────────────────────────────────────────
    public function export(Request $request)
    {
        $tanggal  = $request->get('tanggal', today()->toDateString());
        $filename = "HENKATEN_Absen_{$tanggal}.csv";
        $allRows  = [];

        foreach (['Factory 2', 'Factory 3 & 4'] as $factory) {
            foreach (['A', 'B'] as $shift) {
                $members = $this->membersFor($factory, $shift);
                if ($members->isEmpty()) continue;

                $records = AbsenceRecord::where([
                    'tanggal' => $tanggal,
                    'factory' => $factory,
                    'shift'   => $shift,
                ])->get()->keyBy('member_id');

                $allRows[] = ["REKAP ABSEN — {$factory} Shift {$shift}"];
                $allRows[] = ["Tanggal: {$tanggal}"];
                $allRows[] = [];
                $allRows[] = ['Nama', 'NIK', 'Jabatan', 'Mesin', 'Factory', 'Shift', 'Status', 'Alasan'];

                foreach ($members as $m) {
                    $rec       = $records[$m->id] ?? null;
                    $allRows[] = [
                        $m->nama, $m->nik ?? '-', $m->jabatan, $m->mesin ?? '-',
                        $factory, "Shift {$shift}",
                        $rec?->status === 'absen' ? 'Absen' : 'Hadir',
                        $rec?->status === 'absen' ? ($rec->reason ?? '-') : '-',
                    ];
                }
                $allRows[] = [];
            }
        }

        return Response::stream(function () use ($allRows) {
            $h = fopen('php://output', 'w');
            foreach ($allRows as $r) fputcsv($h, $r);
            fclose($h);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // PRIVATE: Rebuild AbsenceSummary setelah save
    // ══════════════════════════════════════════════════════════════════
    private function rebuildSummary(string $tanggal, string $factory, string $shift): void
    {
        $members     = $this->membersFor($factory, $shift);
        $totalMember = $members->count();

        $records = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
        ])->get()->keyBy('member_id');

        $hadirCount = 0;
        $opCuti = $opSakit = $opIjin = 0;
        $spvCuti = $spvSakit = $spvIjin = 0;

        foreach ($members as $m) {
            $rec = $records[$m->id] ?? null;

            if (!$rec || $rec->status === 'hadir') {
                $hadirCount++;
                continue;
            }

            $jabatanLower = strtolower($m->jabatan ?? '');
            $isPengawas   = str_contains($jabatanLower, 'pengawas')
                         || str_contains($jabatanLower, 'spv')
                         || str_contains($jabatanLower, 'supervisor')
                         || str_contains($jabatanLower, 'foreman');

            $reason = $rec->reason ?? 'Ijin';

            if ($isPengawas) {
                match ($reason) {
                    'Cuti'  => $spvCuti++,
                    'Sakit' => $spvSakit++,
                    default => $spvIjin++,
                };
            } else {
                match ($reason) {
                    'Cuti'  => $opCuti++,
                    'Sakit' => $opSakit++,
                    default => $opIjin++,
                };
            }
        }

        $totalAbsen = $opCuti + $opSakit + $opIjin + $spvCuti + $spvSakit + $spvIjin;

        AbsenceSummary::updateOrCreate(
            [
                'tanggal' => $tanggal,
                'factory' => $factory,
                'shift'   => $shift,
            ],
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