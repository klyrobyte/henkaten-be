<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyAssignment;
use App\Models\Member;
use App\Models\MemberSkill;
use App\Models\AbsenceRecord;
use App\Models\AbsenceSummary;
use App\Services\FactoryConfigService;
use App\Services\ScContext;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * AssignmentController
 * fixed by Rizky
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
/**
 * @group Assignment
 * 
 * APIs for managing Assignment.
 */
class AssignmentController extends Controller
{
    public function __construct(protected FactoryConfigService $factoryConfig)
    {
    }

    /**
     * Helper to get authorized factory based on user role and request
     */
    private function resolveFactory(Request $request): string
    {
        $user = Auth::user();
        $scId = ScContext::id();
        $factory = $request->get('factory') ?: $request->session()->get('factory', ScContext::firstFactory());

        if ($user && !$user->isSuperAdmin()) {
            $allowedFactories = (array) $user->factory;
            if (!empty($allowedFactories) && !in_array($factory, $allowedFactories)) {
                $factory = $allowedFactories[0];
            }
        }

        return $factory;
    }

    // ══════════════════════════════════════════════════════════════════════════
    // HELPER: Normalisasi nilai shift  - pastikan selalu 'A' atau 'B'
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
    // INDEX  - Halaman utama Penugasan Harian
    // Menggantikan: init() + renderBoard() JS
    // ══════════════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $user = Auth::user();
        $scId = ScContext::id();
        $factory = $this->resolveFactory($request);
        $shift = $this->normalizeShift($request->session()->get('shift', 'A'));
        $tanggal = $request->get('tanggal', today()->toDateString());

        // Konfigurasi grup & mesin
        $groups = $this->factoryConfig->getGroups($factory);
        $mesinList = $this->factoryConfig->getAllMachines($factory);

        // Data assignment dari DB
        $assignments = DailyAssignment::toAssignmentsArray($tanggal, $factory, $shift);

        // Jika belum ada assignment hari ini, buat default dari member DB
        if (empty($assignments)) {
            $assignments = $this->buildDefaults($factory, $shift, $groups, $tanggal);
        }

        // Data absen dari AbsenceRecord (untuk auto-sync)
        $absenIds = AbsenceRecord::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'absen',
        ])->pluck('member_id')->toArray();

        // Member list untuk panel cari pengganti  - semua member aktif di factory ini
        $members = Member::where('sc_id', $scId)
            ->where('factory', $factory)
            ->where('status', 'active')
            ->orderBy('nama')
            ->get();

        // Summary counts
        $summary = $this->calcSummary($assignments);

        return view('admin.assignment', compact(
            'factory',
            'shift',
            'tanggal',
            'groups',
            'assignments',
            'members',
            'absenIds',
            'summary',
            'mesinList'
        ));
    }

    // ══════════════════════════════════════════════════════════════════════════
    // SAVE  - Simpan semua assignment
    // Menggantikan: saveAssignments() + broadcast() JS
    // POST /admin/assignment/save
    // ══════════════════════════════════════════════════════════════════════════

    public function save(Request $request): JsonResponse
    {
        $request->validate([
            'tanggal' => 'required|date',
            'factory' => 'required|string',
            'shift' => 'required|string',
            'assignments' => 'required|array',
        ]);

        $user = Auth::user();
        $scId = ScContext::id();
        $factory = $request->factory;

        if ($user && !$user->isSuperAdmin()) {
            $allowedFactories = (array) $user->factory;
            if (!in_array($factory, $allowedFactories)) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized factory access.'], 403);
            }
        }

        $tanggal = $request->tanggal;
        $shift = $this->normalizeShift($request->shift);
        $assignments = $request->assignments;

        DB::transaction(function () use ($tanggal, $factory, $shift, $assignments, $scId) {

            // Hapus semua slot lama untuk konteks ini
            DailyAssignment::where([
                'sc_id' => $scId,
                'tanggal' => $tanggal,
                'factory' => $factory,
                'shift' => $shift,
            ])->delete();

            // Simpan slot baru
            foreach ($assignments as $mcKey => $slots) {
                [$groupTitle, $machineName] = explode('::', $mcKey, 2);

                foreach ($slots as $idx => $slot) {
                    $memberName = $slot['memberName'] ?? '';
                    if (!$memberName)
                        continue;

                    $member = Member::where('sc_id', $scId)
                        ->where('nama', $memberName)
                        ->where('factory', $factory)
                        ->first();

                    DailyAssignment::create([
                        'sc_id' => $scId,
                        'tanggal' => $tanggal,
                        'factory' => $factory,
                        'shift' => $shift,
                        'group_title' => $groupTitle,
                        'machine_name' => $machineName,
                        'slot_index' => $idx,
                        'member_id' => $member?->id,
                        'member_name' => $memberName,
                        'status' => $slot['status'] ?? 'present',
                        'absent_reason' => $slot['absentReason'] ?? null,
                        'is_substitute' => $slot['isSubstitute'] ?? false,
                        'substitute_for' => $slot['substituteFor'] ?? null,
                        'synced_from_mm' => $slot['syncedFromMM'] ?? false,
                    ]);
                }
            }

            // Sync absensi ke absence_records
            $this->broadcastAbsence($tanggal, $factory, $shift, $assignments);
        });

        return response()->json([
            'ok' => true,
            'summary' => $this->calcSummary($assignments),
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // GET DATA  - Ambil assignments sebagai JSON untuk AJAX reload
    // GET /admin/assignment/data
    // ══════════════════════════════════════════════════════════════════════════

    public function getData(Request $request): JsonResponse
    {
        $user = Auth::user();
        $scId = ScContext::id();
        $tanggal = $request->get('tanggal', today()->toDateString());
        $factory = $this->resolveFactory($request);
        $shift = $this->normalizeShift($request->get('shift', session('shift', 'A')));

        $assignments = DailyAssignment::toAssignmentsArray($tanggal, $factory, $shift);

        $absenIds = AbsenceRecord::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'absen',
        ])->pluck('member_id')->toArray();

        return response()->json([
            'assignments' => $assignments,
            'absenIds' => $absenIds,
            'summary' => $this->calcSummary($assignments),
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // CANDIDATES  - Daftar kandidat pengganti
    // FIX: normalisasi shift, gabungkan semua sumber absen, hapus duplikasi logika
    // GET /admin/assignment/candidates
    // ══════════════════════════════════════════════════════════════════════════

    public function candidates(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $scId = ScContext::id();
            $tanggal = $request->get('tanggal', today()->toDateString());
            $factory = $this->resolveFactory($request);
            $shift = $this->normalizeShift($request->get('shift', session('shift', 'A')));

            if ($user && !$user->isSuperAdmin()) {
                if ($user->shift)
                    $shift = $this->normalizeShift($user->shift);
            }

            $q = trim($request->get('q', ''));

            // --- Kumpulkan semua nama yang sedang ABSEN dari semua sumber ---

            // Sumber 1: DailyAssignment (slot yang statusnya absent, bukan pengganti)
            $absentFromDA = DailyAssignment::where([
                'sc_id' => $scId,
                'tanggal' => $tanggal,
                'factory' => $factory,
                'shift' => $shift,
                'status' => 'absent',
            ])->where('is_substitute', false)
                ->pluck('member_name')
                ->toArray();

            // Sumber 2: AbsenceRecord dari halaman Member Management
            $absentFromMM = AbsenceRecord::where([
                'sc_id' => $scId,
                'tanggal' => $tanggal,
                'factory' => $factory,
                'shift' => $shift,
                'status' => 'absen',
            ])->with('member')
                ->get()
                ->pluck('member.nama')
                ->filter()
                ->toArray();

            // Gabungkan & deduplikasi semua nama yang absen
            $allAbsentNames = array_unique(array_merge($absentFromDA, $absentFromMM));

            // --- Kumpulkan nama yang sudah bertugas (hadir & punya assignment) ---
            $workingNames = DailyAssignment::where([
                'sc_id' => $scId,
                'tanggal' => $tanggal,
                'factory' => $factory,
                'shift' => $shift,
                'status' => 'present',
            ])->pluck('member_name')
                ->unique()
                ->toArray();

            $nsActiveShift = \App\Services\NonShiftResolver::activeShiftFor($tanggal);
            $includeNs = ($nsActiveShift === $shift);
            $shifts = [$shift];
            if ($includeNs) {
                $shifts[] = 'NS';
            }

            // --- Query member kandidat ---
            $query = Member::where('sc_id', $scId)
                ->where('status', 'active')
                ->whereIn('shift', $shifts);

            // Kecualikan member yang sedang absen
            if (!empty($allAbsentNames)) {
                $query->whereNotIn('nama', $allAbsentNames);
            }

            // Filter pencarian nama
            if ($q !== '') {
                $query->where('nama', 'like', "%{$q}%");
            }

            $machineName = $request->get('machine');
            $absentName = $request->get('absentName', '');
            $factory = $request->get('factory');

            if (!empty($absentName)) {
                // Pengganti (Red Dot)
                // Member all factory (fac 2 maupun fac 34). Tidak pakai where factory.
            } else {
                // Bukan pengganti (tambah member biasa)
                // Base on factory.
                if ($factory) {
                    $query->where('factory', $factory);
                }
            }

            // Ambil skills kandidat
            $query->with(['skills' => function($q) use ($machineName) {
                $q->where('machine_name', $machineName);
            }]);

            $allCandidates = $query->orderBy('nama')->get();

            // Load absent member's skills if this is a replacement
            $absentSkills = collect();
            if (!empty($absentName)) {
                $absentMember = Member::where('sc_id', $scId)->where('nama', $absentName)->first();
                if ($absentMember) {
                    $absentSkills = MemberSkill::where('member_id', $absentMember->id)
                        ->where('machine_name', $machineName)
                        ->where('skill_pct', '>', 0)
                        ->get()
                        ->keyBy(function($s) {
                            return empty($s->process_name) ? '-' : $s->process_name;
                        });
                }
            }

            $members = $allCandidates->map(function($m) use ($workingNames, $absentSkills, $absentName) {
                $isWorking = in_array($m->nama, $workingNames);
                $isEligible = true;
                
                if (!empty($absentName) && $absentSkills->isNotEmpty()) {
                    // Check if candidate fulfills ALL absent member's skills on this machine
                    $candSkills = $m->skills->keyBy(function($s) {
                        return empty($s->process_name) ? '-' : $s->process_name;
                    });
                    
                    foreach ($absentSkills as $proc => $aSkill) {
                        $cSkillPct = isset($candSkills[$proc]) ? $candSkills[$proc]->skill_pct : 0;
                        
                        // Syarat mutlak: kandidat harus punya skill MINIMAL 75% pada proses ini
                        // (Meskipun member absen punya 100%, 75% sudah dianggap memenuhi standar)
                        if ($cSkillPct < 75) {
                            $isEligible = false;
                            break;
                        }
                    }
                } else {
                    // Jika bukan pengganti, atau member absen tidak punya data skill:
                    // Kandidat wajib punya setidaknya satu skill >= 75% di mesin ini
                    $maxPct = $m->skills->max('skill_pct') ?? 0;
                    $isEligible = $maxPct >= 75;
                }

                return [
                    'id' => $m->id,
                    'name' => $m->nama,
                    'jabatan' => $m->jabatan,
                    'photo' => $m->photo_url,
                    'mesin' => $m->mesin,
                    'isWorking' => $isWorking,
                    'skill_pct' => $m->skills->max('skill_pct') ?? 0,
                    'eligible' => $isEligible,
                ];
            });

            // HANYA MUNCULKAN YANG MEMENUHI SYARAT (eligible)
            // Diluar itu jangan dimunculkan sama sekali (per request)
            $members = $members->filter(function($m) {
                return $m['eligible'] === true;
            });

            // Urutkan: yang belum bertugas duluan

            $members = $members->sortBy(function($m) {
                return ($m['isWorking'] ? 100 : 0) + ($m['eligible'] ? 0 : 10);
            })->values();

            return response()->json($members);

        } catch (\Throwable $e) {
            \Log::error('AssignmentController@candidates failed', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['message' => 'Internal Server Error'], 500);
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    // SYNC ABSEN  - Sinkronkan absen dari AbsenceRecord → assignment
    // POST /admin/assignment/sync-absen
    // ══════════════════════════════════════════════════════════════════════════

    public function syncAbsen(Request $request): JsonResponse
    {
        $user = Auth::user();
        $scId = ScContext::id();
        $tanggal = $request->get('tanggal', today()->toDateString());
        $factory = $this->resolveFactory($request);
        $shift = $this->normalizeShift($request->get('shift', session('shift', 'A')));

        $absenRecords = AbsenceRecord::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'absen',
        ])->with('member')->get();

        $synced = 0;

        foreach ($absenRecords as $rec) {
            if (!$rec->member)
                continue;
            $nama = $rec->member->nama;

            $updated = DailyAssignment::where([
                'sc_id' => $scId,
                'tanggal' => $tanggal,
                'factory' => $factory,
                'shift' => $shift,
                'member_name' => $nama,
                'status' => 'present',
                'is_substitute' => false,
            ])->update([
                        'status' => 'absent',
                        'absent_reason' => $rec->reason ?? 'Absen',
                        'synced_from_mm' => true,
                    ]);

            $synced += $updated;
        }

        return response()->json(['ok' => true, 'synced' => $synced]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════════════

    private function buildDefaults(string $factory, string $shift, array $groups, string $tanggal): array
    {
        $scId = ScContext::id();

        $nsActiveShift = \App\Services\NonShiftResolver::activeShiftFor($tanggal);
        $includeNs = ($nsActiveShift === $shift);
        $shifts = [$shift];
        if ($includeNs) {
            $shifts[] = 'NS';
        }

        $membersByMachine = Member::where('sc_id', $scId)
            ->where('factory', $factory)
            ->where('status', 'active')
            ->whereIn('shift', $shifts)
            ->orderBy('id')
            ->get()
            ->groupBy('mesin');

        $assignments = [];

        foreach ($groups as $group) {
            foreach ($group['machines'] as $machine) {
                $key = "{$group['title']}::{$machine}";
                
                // Ambil member yang memang di-assign ke mesin ini di Member Management
                $machineMembers = $membersByMachine->get($machine, collect())->values();
                $count = max(1, count($machineMembers));

                $slots = [];
                for ($i = 0; $i < $count; $i++) {
                    // Ambil member sesuai index jika ada, jika tidak biarkan kosong
                    $m = $machineMembers->get($i);
                    $slots[] = [
                        'memberName' => $m?->nama ?? '',
                        'foto' => $m?->photo_url,
                        'status' => 'present',
                        'absentReason' => '',
                        'isSubstitute' => false,
                        'substituteFor' => null,
                        'syncedFromMM' => false,
                        'memberId' => $m?->id,
                    ];
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
                if (!($slot['memberName'] ?? ''))
                    continue;
                if ($slot['isSubstitute'] ?? false)
                    continue;

                if (($slot['status'] ?? 'present') === 'absent') {
                    $absen++;
                    $butuh++;
                    $hasSub = collect($slots)->contains(
                        fn($s) =>
                        ($s['isSubstitute'] ?? false) && ($s['substituteFor'] ?? null) === $i
                    );
                    if ($hasSub)
                        $terisi++;
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
        $scId = ScContext::id();
        $reasonMap = [
            'Sakit' => 'Sakit',
            'Cuti' => 'Cuti',
            'Ijin' => 'Ijin',
            'Alpha' => 'Alpha',
            'Absen' => 'Alpha',
            'Absen (Daily Assign)' => 'Alpha',
        ];

        $absentSlots = [];
        $hadirNames = [];

        foreach ($assignments as $slots) {
            foreach ($slots as $slot) {
                $name = $slot['memberName'] ?? '';
                if (!$name || ($slot['isSubstitute'] ?? false))
                    continue;
                if (($slot['status'] ?? 'present') === 'absent') {
                    $absentSlots[$name] = $slot['absentReason'] ?? 'Absen';
                } else {
                    $hadirNames[] = $name;
                }
            }
        }
        $hadirNames = array_unique($hadirNames);

        foreach ($absentSlots as $name => $rawReason) {
            $member = Member::where('sc_id', $scId)->where('nama', $name)->where('factory', $factory)->first();
            if (!$member)
                continue;

            $dbReason = $reasonMap[$rawReason] ?? 'Alpha';

            AbsenceRecord::updateOrCreate(
                ['sc_id' => $scId, 'tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift, 'member_id' => $member->id],
                ['status' => 'absen', 'reason' => $dbReason]
            );
        }

        foreach ($hadirNames as $name) {
            $member = Member::where('sc_id', $scId)->where('nama', $name)->where('factory', $factory)->first();
            if (!$member)
                continue;
            AbsenceRecord::where([
                'sc_id' => $scId,
                'tanggal' => $tanggal,
                'factory' => $factory,
                'shift' => $shift,
                'member_id' => $member->id,
            ])->delete();
        }

        $totalAbsen = count($absentSlots);
        $totalHadir = count($hadirNames);
        $totalMember = $totalAbsen + $totalHadir;

        AbsenceSummary::updateOrCreate(
            ['sc_id' => $scId, 'tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift],
            [
                'total_member' => $totalMember,
                'mp_hadir' => $totalHadir,
                'mp_absen' => $totalAbsen,
                'total_absen' => $totalAbsen,
                'source' => 'dailyassignment',
            ]
        );
    }
}
