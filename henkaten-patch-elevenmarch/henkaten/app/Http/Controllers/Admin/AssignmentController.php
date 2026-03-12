<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyAssignment;
use App\Models\Member;
use App\Models\AbsenceRecord;
use App\Models\AbsenceSummary;
use App\Services\FactoryConfigService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * AssignmentController
 *
 * Menggantikan seluruh logika JS di dailyassignment.html:
 *
 *   JS function                  → Laravel method
 *   ─────────────────────────────────────────────────
 *   init() + loadAssignments()   → index()
 *   saveAssignments()            → save()          POST /admin/assignment/save
 *   syncAbsenFromMemberMgmt()    → syncAbsen()     GET  /admin/assignment/sync-absen (AJAX)
 *   renderCandidates()           → candidates()    GET  /admin/assignment/candidates  (AJAX)
 *   broadcast()                  → (dipanggil di dalam save())
 *   loadAssignments() AJAX       → getData()       GET  /admin/assignment/data        (AJAX)
 */
class AssignmentController extends Controller
{
    public function __construct(protected FactoryConfigService $factoryConfig) {}

    // ══════════════════════════════════════════════════════════════════════════
    // HELPER: Normalisasi nilai shift — pastikan selalu 'A' atau 'B'
    // Mengatasi inkonsistensi nilai 'Shift A' vs 'A' dari berbagai sumber
    // ══════════════════════════════════════════════════════════════════════════

    private function normalizeShift(string $shift): string
    {
        // Hapus prefix 'Shift ' jika ada, ambil karakter pertama huruf besar
        $shift = trim(str_ireplace('shift', '', $shift));
        $shift = strtoupper(trim($shift));
        return in_array($shift, ['A', 'B']) ? $shift : 'A';
    }

    // ══════════════════════════════════════════════════════════════════════════
    // INDEX — Halaman utama Penugasan Harian
    // Menggantikan: init() + renderBoard() JS
    // ══════════════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $factory = $request->session()->get('factory', 'Factory 2');
        $shift   = $this->normalizeShift($request->session()->get('shift', 'A'));
        $tanggal = $request->get('tanggal', today()->toDateString());

        // Konfigurasi grup & mesin
        $groups    = $this->factoryConfig->getGroups($factory);
        $mesinList = $this->factoryConfig->getAllMachines($factory);

        // Data assignment dari DB
        $assignments = DailyAssignment::toAssignmentsArray($tanggal, $factory, $shift);

        // Jika belum ada assignment hari ini, buat default dari member DB
        if (empty($assignments)) {
            $assignments = $this->buildDefaults($factory, $shift, $groups);
        }

        // Data absen dari AbsenceRecord (untuk auto-sync)
        $absenIds = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
            'status'  => 'absen',
        ])->pluck('member_id')->toArray();

        // Member list untuk panel cari pengganti — semua member aktif di factory ini
        $members = Member::where('factory', $factory)
            ->where('status', 'active')
            ->orderBy('nama')
            ->get();

        // Summary counts
        $summary = $this->calcSummary($assignments);

        return view('admin.assignment', compact(
            'factory', 'shift', 'tanggal',
            'groups', 'assignments', 'members',
            'absenIds', 'summary', 'mesinList'
        ));
    }

    // ══════════════════════════════════════════════════════════════════════════
    // SAVE — Simpan semua assignment
    // Menggantikan: saveAssignments() + broadcast() JS
    // POST /admin/assignment/save
    // ══════════════════════════════════════════════════════════════════════════

    public function save(Request $request): JsonResponse
    {
        $request->validate([
            'tanggal'     => 'required|date',
            'factory'     => 'required|string',
            'shift'       => 'required|string',
            'assignments' => 'required|array',
        ]);

        $tanggal     = $request->tanggal;
        $factory     = $request->factory;
        $shift       = $this->normalizeShift($request->shift);
        $assignments = $request->assignments;

        DB::transaction(function () use ($tanggal, $factory, $shift, $assignments) {

            // Hapus semua slot lama untuk konteks ini
            DailyAssignment::where([
                'tanggal' => $tanggal,
                'factory' => $factory,
                'shift'   => $shift,
            ])->delete();

            // Simpan slot baru
            foreach ($assignments as $mcKey => $slots) {
                [$groupTitle, $machineName] = explode('::', $mcKey, 2);

                foreach ($slots as $idx => $slot) {
                    $memberName = $slot['memberName'] ?? '';
                    if (!$memberName) continue;

                    $member = Member::where('nama', $memberName)
                                    ->where('factory', $factory)
                                    ->first();

                    DailyAssignment::create([
                        'tanggal'       => $tanggal,
                        'factory'       => $factory,
                        'shift'         => $shift,
                        'group_title'   => $groupTitle,
                        'machine_name'  => $machineName,
                        'slot_index'    => $idx,
                        'member_id'     => $member?->id,
                        'member_name'   => $memberName,
                        'status'        => $slot['status'] ?? 'present',
                        'absent_reason' => $slot['absentReason'] ?? null,
                        'is_substitute' => $slot['isSubstitute'] ?? false,
                        'substitute_for'=> $slot['substituteFor'] ?? null,
                        'synced_from_mm'=> $slot['syncedFromMM'] ?? false,
                    ]);
                }
            }

            // Sync absensi ke absence_records
            $this->broadcastAbsence($tanggal, $factory, $shift, $assignments);
        });

        return response()->json([
            'ok'      => true,
            'summary' => $this->calcSummary($assignments),
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // GET DATA — Ambil assignments sebagai JSON untuk AJAX reload
    // GET /admin/assignment/data
    // ══════════════════════════════════════════════════════════════════════════

    public function getData(Request $request): JsonResponse
    {
        $tanggal = $request->get('tanggal', today()->toDateString());
        $factory = $request->get('factory', session('factory', 'Factory 2'));
        $shift   = $this->normalizeShift($request->get('shift', session('shift', 'A')));

        $assignments = DailyAssignment::toAssignmentsArray($tanggal, $factory, $shift);

        $absenIds = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
            'status'  => 'absen',
        ])->pluck('member_id')->toArray();

        return response()->json([
            'assignments' => $assignments,
            'absenIds'    => $absenIds,
            'summary'     => $this->calcSummary($assignments),
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // CANDIDATES — Daftar kandidat pengganti
    // FIX: normalisasi shift, gabungkan semua sumber absen, hapus duplikasi logika
    // GET /admin/assignment/candidates
    // ══════════════════════════════════════════════════════════════════════════

    public function candidates(Request $request): JsonResponse
    {
        try {
        $tanggal = $request->get('tanggal', today()->toDateString());
        $factory = $request->get('factory', session('factory', 'Factory 2'));
        $shift   = $this->normalizeShift($request->get('shift', session('shift', 'A')));
        $q       = trim($request->get('q', ''));

        // --- Kumpulkan semua nama yang sedang ABSEN dari semua sumber ---

        // Sumber 1: DailyAssignment (slot yang statusnya absent, bukan pengganti)
        $absentFromDA = DailyAssignment::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
            'status'  => 'absent',
        ])->where('is_substitute', false)
          ->pluck('member_name')
          ->toArray();

        // Sumber 2: AbsenceRecord dari halaman Member Management
        $absentFromMM = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
            'status'  => 'absen',
        ])->with('member')
          ->get()
          ->pluck('member.nama')
          ->filter()
          ->toArray();

        // Gabungkan & deduplikasi semua nama yang absen
        $allAbsentNames = array_unique(array_merge($absentFromDA, $absentFromMM));

        // --- Kumpulkan nama yang sudah bertugas (hadir & punya assignment) ---
        $workingNames = DailyAssignment::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
            'status'  => 'present',
        ])->pluck('member_name')
          ->unique()
          ->toArray();

        // --- Query member kandidat ---
        $query = Member::where('factory', $factory)
            ->where('status', 'active');

        // Kecualikan member yang sedang absen
        if (!empty($allAbsentNames)) {
            $query->whereNotIn('nama', $allAbsentNames);
        }

        // Filter pencarian nama
        if ($q !== '') {
            $query->where('nama', 'like', "%{$q}%");
        }

        $members = $query->orderBy('nama')->get()->map(fn($m) => [
            'id'        => $m->id,
            'name'      => $m->nama,
            'photo'     => $m->photo ? asset('storage/' . $m->photo) : null,
            'jabatan'   => $m->jabatan,
            'mesin'     => $m->mesin,
            'isWorking' => in_array($m->nama, $workingNames),
        ]);

        // Urutkan: yang belum bertugas duluan
        $members = $members->sortBy('isWorking')->values();

        // Debug mode: akses dengan ?debug=1
        if ($request->get('debug') == '1') {
            return response()->json([
                'debug' => [
                    'factory'        => $factory,
                    'shift'          => $shift,
                    'shift_raw'      => $request->get('shift'),
                    'tanggal'        => $tanggal,
                    'q'              => $q,
                    'totalActive'    => Member::where('factory', $factory)->where('status', 'active')->count(),
                    'absentFromDA'   => $absentFromDA,
                    'absentFromMM'   => $absentFromMM,
                    'allAbsentNames' => $allAbsentNames,
                    'workingNames'   => $workingNames,
                    'resultCount'    => $members->count(),
                    'result'         => $members,
                ]
            ]);
        }

        return response()->json($members);

        } catch (\Throwable $e) {
            // Sementara: expose error asli untuk debugging — HAPUS setelah fix
            return response()->json([
                'error'   => true,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => collect($e->getTrace())->take(5)->map(fn($t) => [
                    'file' => $t['file'] ?? '',
                    'line' => $t['line'] ?? '',
                    'function' => $t['function'] ?? '',
                ])->toArray(),
            ], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    // SYNC ABSEN — Sinkronkan absen dari AbsenceRecord → assignment
    // POST /admin/assignment/sync-absen
    // ══════════════════════════════════════════════════════════════════════════

    public function syncAbsen(Request $request): JsonResponse
    {
        $tanggal = $request->get('tanggal', today()->toDateString());
        $factory = $request->get('factory', session('factory', 'Factory 2'));
        $shift   = $this->normalizeShift($request->get('shift', session('shift', 'A')));

        $absenRecords = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
            'status'  => 'absen',
        ])->with('member')->get();

        $synced = 0;

        foreach ($absenRecords as $rec) {
            if (!$rec->member) continue;
            $nama = $rec->member->nama;

            $updated = DailyAssignment::where([
                'tanggal'       => $tanggal,
                'factory'       => $factory,
                'shift'         => $shift,
                'member_name'   => $nama,
                'status'        => 'present',
                'is_substitute' => false,
            ])->update([
                'status'         => 'absent',
                'absent_reason'  => $rec->reason ?? 'Absen',
                'synced_from_mm' => true,
            ]);

            $synced += $updated;
        }

        return response()->json(['ok' => true, 'synced' => $synced]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════════════

    private function buildDefaults(string $factory, string $shift, array $groups): array
    {
        $members = Member::where('factory', $factory)
            ->where('status', 'active')
            ->orderBy('id')
            ->get();

        $assignments = [];
        $memberIndex = 0;

        foreach ($groups as $group) {
            foreach ($group['machines'] as $machine) {
                $key   = "{$group['title']}::{$machine}";
                $count = $this->factoryConfig->getCircleCount($factory, $group['title'], $machine);

                $slots = [];
                for ($i = 0; $i < $count; $i++) {
                    $m = $members[$memberIndex] ?? null;
                    $slots[] = [
                        'memberName'    => $m?->nama ?? '',
                        'foto'          => $m?->photo ? asset('storage/' . $m->photo) : null,
                        'status'        => 'present',
                        'absentReason'  => '',
                        'isSubstitute'  => false,
                        'substituteFor' => null,
                        'syncedFromMM'  => false,
                        'memberId'      => $m?->id,
                    ];
                    $memberIndex++;
                }

                $assignments[$key] = $slots;
            }
        }

        return $assignments;
    }

    private function calcSummary(array $assignments): array
    {
        $hadir = $absen = $butuh = $terisi = 0;

        foreach ($assignments as $slots) {
            foreach ($slots as $i => $slot) {
                if (!($slot['memberName'] ?? '')) continue;
                if ($slot['isSubstitute'] ?? false) continue;

                if (($slot['status'] ?? 'present') === 'absent') {
                    $absen++;
                    $butuh++;
                    $hasSub = collect($slots)->contains(fn($s) =>
                        ($s['isSubstitute'] ?? false) && ($s['substituteFor'] ?? null) === $i
                    );
                    if ($hasSub) $terisi++;
                } else {
                    $hadir++;
                }
            }
        }

        $kosong = max(0, $butuh - $terisi);

        return compact('hadir', 'absen', 'butuh', 'terisi', 'kosong');
    }

    private function broadcastAbsence(string $tanggal, string $factory, string $shift, array $assignments): void
    {
        $reasonMap = [
            'Sakit' => 'Sakit',
            'Cuti'  => 'Cuti',
            'Ijin'  => 'Ijin',
            'Alpha' => 'Mangkir',
            'Absen' => 'Mangkir',
            'Absen (Daily Assign)' => 'Mangkir',
        ];

        $absentSlots = [];
        $hadirNames  = [];

        foreach ($assignments as $slots) {
            foreach ($slots as $slot) {
                $name = $slot['memberName'] ?? '';
                if (!$name || ($slot['isSubstitute'] ?? false)) continue;
                if (($slot['status'] ?? 'present') === 'absent') {
                    $absentSlots[$name] = $slot['absentReason'] ?? 'Absen';
                } else {
                    $hadirNames[] = $name;
                }
            }
        }
        $hadirNames = array_unique($hadirNames);

        foreach ($absentSlots as $name => $rawReason) {
            $member = Member::where('nama', $name)->where('factory', $factory)->first();
            if (!$member) continue;

            $dbReason = $reasonMap[$rawReason] ?? 'Mangkir';

            AbsenceRecord::updateOrCreate(
                ['tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift, 'member_id' => $member->id],
                ['status' => 'absen', 'reason' => $dbReason]
            );
        }

        foreach ($hadirNames as $name) {
            $member = Member::where('nama', $name)->where('factory', $factory)->first();
            if (!$member) continue;
            AbsenceRecord::where([
                'tanggal'   => $tanggal,
                'factory'   => $factory,
                'shift'     => $shift,
                'member_id' => $member->id,
            ])->delete();
        }

        $totalAbsen  = count($absentSlots);
        $totalHadir  = count($hadirNames);
        $totalMember = $totalAbsen + $totalHadir;

        AbsenceSummary::updateOrCreate(
            ['tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift],
            [
                'mp_hadir'     => $totalHadir,
                'mp_absen'     => $totalAbsen,
                'p_cuti'       => 0,
                'p_sakit'      => 0,
                'p_ijin'       => 0,
                'o_cuti'       => 0,
                'o_sakit'      => 0,
                'o_ijin'       => 0,
                'total_absen'  => $totalAbsen,
                'total_member' => $totalMember,
                'source'       => 'dailyassignment',
            ]
        );
    }
}