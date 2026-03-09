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
    // ──────────────────────────────────────────────────────────────────
    // HALAMAN INPUT ABSEN
    // GET /admin/absence
    // ──────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $tanggal = $request->get('tanggal', today()->toDateString());
        $factory = $request->get('factory', 'Factory 2');
        $shift   = $request->get('shift',   'A');

        $members = Member::where('factory', $factory)
            ->where('shift', $shift)
            ->where('status', 'active')
            ->orderBy('nama')
            ->get();

        $records = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
        ])->get()->keyBy('member_id');

        $hadir = $members->filter(fn($m) => ($records[$m->id]?->status ?? 'hadir') === 'hadir')->count();
        $absen = $members->count() - $hadir;

        return view('admin.member.absen',
            compact('members', 'records', 'tanggal', 'factory', 'shift', 'hadir', 'absen'));
    }

    // ──────────────────────────────────────────────────────────────────
    // SIMPAN ABSEN BATCH
    // POST /admin/absence/save
    //
    // Body: { tanggal, factory, shift, records: { memberId: { status, reason, substitute } } }
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
        $factory = $request->factory;
        $shift   = $request->shift;

        DB::transaction(function () use ($request, $tanggal, $factory, $shift) {
            foreach ($request->records as $memberId => $rec) {
                AbsenceRecord::updateOrCreate(
                    [
                        'tanggal'   => $tanggal,
                        'factory'   => $factory,
                        'shift'     => $shift,
                        'member_id' => $memberId,
                    ],
                    [
                        'status'    => $rec['status'] ?? 'hadir',
                        'reason'    => ($rec['status'] ?? '') === 'absen' ? ($rec['reason'] ?? null) : null,
                        // Simpan nama pengganti jika ada (field opsional, tambahkan kolom jika belum ada)
                        // 'substitute_name' => ($rec['status'] ?? '') === 'absen' ? ($rec['substitute']['name'] ?? null) : null,
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

    // ──────────────────────────────────────────────────────────────────
    // KANDIDAT PENGGANTI — dipanggil dari panel 🔍 di absen.blade.php
    // Algoritma identik dengan renderCandidates() di dailyassignment.html:
    //   1. Kecualikan member yang absen di hari ini
    //   2. Tandai member yang sudah bertugas (isWorking)
    //   3. Sort: yang belum bertugas muncul duluan
    //
    // GET /admin/absence/candidates?tanggal=&factory=&shift=&q=&for_member=
    // ──────────────────────────────────────────────────────────────────
    public function candidates(Request $request)
    {
        $tanggal   = $request->get('tanggal', today()->toDateString());
        $factory   = $request->get('factory', 'Factory 2');
        $shift     = $request->get('shift', 'A');
        $q         = strtolower(trim($request->get('q', '')));
        $forMember = $request->get('for_member', ''); // nama member yang dicari penggantinya

        // 1. Nama yang absen hari ini (tidak boleh jadi pengganti)
        $absentIds = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
            'status'  => 'absen',
        ])->pluck('member_id')->toArray();

        // 2. Semua member aktif di factory+shift ini, kecuali yang absen
        $query = Member::where('factory', $factory)
            ->where('status', 'active')
            ->whereIn('shift', [$shift, 'AB'])
            ->whereNotIn('id', $absentIds)
            ->orderBy('nama');

        if ($q) {
            $query->where('nama', 'like', "%{$q}%");
        }

        $allMembers = $query->get();

        // 3. Tandai siapa yang sudah ada di daily_assignments (sudah bertugas)
        //    Fallback: cek dari absence_records — hadir = sudah bertugas
        $workingIds = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
            'status'  => 'hadir',
        ])->pluck('member_id')->toArray();

        // Jika daily_assignments model tersedia, gunakan juga
        $workingNamesFromDA = [];
        if (class_exists(\App\Models\DailyAssignment::class)) {
            $workingNamesFromDA = \App\Models\DailyAssignment::where([
                'tanggal' => $tanggal,
                'factory' => $factory,
                'shift'   => $shift,
                'status'  => 'present',
            ])->pluck('member_name')->toArray();
        }

        $result = $allMembers->map(fn($m) => [
            'id'        => $m->id,
            'name'      => $m->nama,
            'photo'     => $m->photo_url,
            'jabatan'   => $m->jabatan,
            'isWorking' => in_array($m->id, $workingIds) || in_array($m->nama, $workingNamesFromDA),
        ]);

        // Sort: yang belum bertugas (isWorking=false) tampil duluan — sama seperti JS lama
        $result = $result->sortBy('isWorking')->values();

        return response()->json($result);
    }

    // ──────────────────────────────────────────────────────────────────
    // DATA ABSEN JSON (AJAX)
    // GET /admin/absence/data
    // ──────────────────────────────────────────────────────────────────
    public function getData(Request $request)
    {
        $records = AbsenceRecord::where([
            'tanggal' => $request->tanggal,
            'factory' => $request->factory,
            'shift'   => $request->shift,
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
                $members = Member::where(['factory' => $factory, 'shift' => $shift, 'status' => 'active'])
                    ->orderBy('nama')->get();
                if ($members->isEmpty()) continue;

                $records = AbsenceRecord::where(['tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift])
                    ->get()->keyBy('member_id');

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
    // EXPORT REKAP CSV
    // GET /admin/absence/export
    // ──────────────────────────────────────────────────────────────────
    public function export(Request $request)
    {
        $tanggal  = $request->get('tanggal', today()->toDateString());
        $filename = "HENKATEN_Absen_{$tanggal}.csv";
        $allRows  = [];

        foreach (['Factory 2', 'Factory 3 & 4'] as $factory) {
            foreach (['A', 'B'] as $shift) {
                $members = Member::where(['factory' => $factory, 'shift' => $shift, 'status' => 'active'])
                    ->orderBy('nama')->get();
                if ($members->isEmpty()) continue;

                $records = AbsenceRecord::where(['tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift])
                    ->get()->keyBy('member_id');

                $allRows[] = ["REKAP ABSEN — {$factory} Shift {$shift}"];
                $allRows[] = ["Tanggal: {$tanggal}"];
                $allRows[] = [];
                $allRows[] = ['Nama', 'NIK', 'Jabatan', 'Mesin', 'Factory', 'Shift', 'Status', 'Alasan'];

                foreach ($members as $m) {
                    $rec = $records[$m->id] ?? null;
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

    // ── Private helper ────────────────────────────────────────────────

    private function rebuildSummary(string $tanggal, string $factory, string $shift): void
    {
        $members = Member::where(['factory' => $factory, 'shift' => $shift, 'status' => 'active'])->get();
        $records = AbsenceRecord::where(['tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift])
            ->get()->keyBy('member_id');

        $hadirCount = 0;
        $cuti = $sakit = $ijin = 0;

        foreach ($members as $m) {
            $rec = $records[$m->id] ?? null;
            if (!$rec || $rec->status === 'hadir') {
                $hadirCount++;
                continue;
            }
            match ($rec->reason) {
                'Cuti'  => $cuti++,
                'Sakit' => $sakit++,
                default => $ijin++,
            };
        }

        $totalAbsen = $cuti + $sakit + $ijin;

        AbsenceSummary::updateOrCreate(
            ['tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift],
            [
                'mp_hadir'     => $hadirCount,
                'mp_absen'     => $totalAbsen,
                'p_cuti'       => $cuti,
                'p_sakit'      => $sakit,
                'p_ijin'       => $ijin,
                'o_cuti'       => 0,
                'o_sakit'      => 0,
                'o_ijin'       => 0,
                'total_absen'  => $totalAbsen,
                'total_member' => $members->count(),
                'source'       => 'membermanagement',
            ]
        );
    }
}