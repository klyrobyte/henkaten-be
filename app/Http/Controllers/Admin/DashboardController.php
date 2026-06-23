<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsenceSummary;
use App\Models\AbsenceRecord;
use App\Models\AssignmentReplacement;
use App\Models\Factory;
use App\Models\Machine;
use App\Models\Member;
use App\Models\ProblemLog;
use App\Services\FactoryConfigService;
use App\Services\ScContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @group Dashboard
 * 
 * APIs for managing Dashboard.
 */
class DashboardController extends Controller
{
    protected FactoryConfigService $factoryConfig;

    public function __construct(FactoryConfigService $factoryConfig)
    {
        $this->factoryConfig = $factoryConfig;
    }

    // =========================================================================
    //  PUBLIC ROUTES : @rizky
    // =========================================================================

    public function index(Request $request)
    {
        // Resolve factory from session; fall back to user's assigned factory,
        // then to the first factory in DB (never hardcode 'Factory 2').
        $user = Auth::user();
        $scId = ScContext::id();
        $allowedFactories = (!$user->isSuperAdmin() && !empty($user->factory)) ? (array) $user->factory : [];

        $sessionFactory = $request->session()->get('factory');
        if (is_array($sessionFactory)) {
            $sessionFactory = !empty($sessionFactory) ? $sessionFactory[0] : null;
            if ($sessionFactory) {
                $request->session()->put('factory', $sessionFactory);
            }
        }

        // Ensure sessionFactory is allowed if restricted
        if (!empty($allowedFactories) && !in_array($sessionFactory, $allowedFactories)) {
            $sessionFactory = $allowedFactories[0];
            $request->session()->put('factory', $sessionFactory);
        }

        // Delegate to the canonical resolver — carries the strict 403 abort guard
        // so a null can never propagate downstream into typed private methods.
        $factory = ScContext::resolveFactory($sessionFactory, $user);

        $shift = $request->session()->get('shift', $user?->shift ?? 'A');
        $tanggal = $request->get('tanggal', today()->toDateString());

        $factoryObj = \App\Models\Factory::where('sc_id', $scId)->where('name', $factory)->first();
        $factoryDetails = $factoryObj?->detail_departemen;

        $machineStatuses = $this->getMachineStatuses($tanggal, $factory, $shift);
        $machineSummary = $this->buildSummary($machineStatuses, $tanggal, $factory, $shift);

        $absenceSummary = AbsenceSummary::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
        ])->first();

        $openLogsCount = ProblemLog::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'open',
        ])->whereIn('jenis', ['Machine', 'Material', 'Method'])->count();

        // Status level berbasis KY limit vs Total Absen (Task 1 overhaul)
        $totalAbsen = $machineSummary['man'] ?? 0;
        $kyTotalCount = $this->calcKyTotalCount($factory, $shift);
        $statusLevel = $this->calcStatusLevel(
            $totalAbsen,
            $kyTotalCount,
            $openLogsCount
        );

        $groups = $this->factoryConfig->buildGroups($factory);
        $members = Member::where('sc_id', $scId)
            ->where('factory', $factory)
            ->where('shift', $shift)
            ->where('status', 'active')
            ->orderBy('id')->get();

        $machinePhotos = Machine::where('sc_id', $scId)->where('factory', $factory)->get()->keyBy('name');

        // KODE total_mc  - Count only machines with status='mesin' (production machines)
        // Used for initial page load; frontend updates it via API response (buildSummary)
        $total_mc = Machine::where('sc_id', $scId)
            ->where('factory', $factory)
            ->where('status', 'mesin')
            ->count();

        // TOTAL MP: counts active members only — NOT affected by absence/attendance data
        $total_mp = Member::where('sc_id', $scId)
            ->where('factory', $factory)
            ->whereIn('shift', [$shift, 'AB'])
            ->where('status', 'active')->count();


        $statuses = collect($machineStatuses)->map(fn($s) => (object) $s);
        $factories = $this->factoryConfig->getFactoryObjects();

        if (!empty($allowedFactories)) {
            $factories = $factories->whereIn('name', $allowedFactories);
        }

        $repairDepartments = \App\Models\RepairDepartment::where('sc_id', $scId)->get();

        return view('admin.dashboard', compact(
            'factory',
            'shift',
            'tanggal',
            'machineSummary',
            'absenceSummary',
            'openLogsCount',
            'statusLevel',
            'groups',
            'statuses',
            'members',
            'machinePhotos',
            'total_mp',
            'total_mc',
            'factories',
            'repairDepartments',
            'factoryDetails'
        ));

    }


    // =========================================================================
    //  TV MODE
    // =========================================================================

    /**
     * TV Mode  - tampilan read-only fullscreen untuk layar TV di factory floor.
     * Designed and developed by @rizky
     * Akses via: GET /admin/tv?factory=Factory+2&shift=A[&tanggal=YYYY-MM-DD]
     * Dibuka sebagai tab baru dari sidebar drawer  - tidak menggunakan session
     * factory/shift agar setiap TV bisa menampilkan factory berbeda secara mandiri.
     *
     * Data yang dikirim identik dengan index(), sehingga semua logika
     * status mesin, absen, pengganti, dan summary sudah konsisten.
     */
    public function tvMode(Request $request)
    {
        // TV mode pakai query-string, bukan session  - bisa beda per tab/TV
        $user = Auth::user();
        $scId = ScContext::id();
        $allowedFactories = (!$user->isSuperAdmin() && !empty($user->factory)) ? (array) $user->factory : [];

        // TV mode: query-string factory param takes priority (each TV tab can show a
        // different factory independently), then falls through to the session chain.
        $tvSessionFactory = $request->session()->get('factory');
        if (is_array($tvSessionFactory)) {
            $tvSessionFactory = !empty($tvSessionFactory) ? $tvSessionFactory[0] : null;
        }
        $tvRequestedFactory = $request->get('factory');
        if (is_array($tvRequestedFactory)) {
            $tvRequestedFactory = !empty($tvRequestedFactory) ? $tvRequestedFactory[0] : null;
        }
        $factory = ScContext::resolveRequestFactory($tvRequestedFactory, $tvSessionFactory, $user);

        $shift = $request->get('shift', $request->session()->get('shift', 'A'));
        $tanggal = $request->get('tanggal', today()->toDateString());

        $factoryObj = Factory::where('sc_id', $scId)->where('name', $factory)->first();
        $factoryDetails = $factoryObj?->detail_departemen;

        $machineStatuses = $this->getMachineStatuses($tanggal, $factory, $shift);
        $machineSummary = $this->buildSummary($machineStatuses, $tanggal, $factory, $shift);

        $absenceSummary = AbsenceSummary::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
        ])->first();

        $openLogsCount = ProblemLog::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'open',
        ])->whereIn('jenis', ['Machine', 'Material', 'Method'])->count();

        // Status level berbasis KY limit vs Total Absen (Task 1 overhaul)
        $totalAbsen = $absenceSummary->total_absen ?? 0;
        $kyTotalCount = $this->calcKyTotalCount($factory, $shift);
        $statusLevel = $this->calcStatusLevel(
            $totalAbsen,
            $kyTotalCount,
            $openLogsCount
        );

        $groups = $this->factoryConfig->buildGroups($factory);
        $members = Member::where('sc_id', $scId)
            ->where('factory', $factory)
            ->where('shift', $shift)
            ->where('status', 'active')
            ->orderBy('id')->get();

        $machinePhotos = Machine::where('sc_id', $scId)->where('factory', $factory)->get()->keyBy('name');
        $statuses = collect($machineStatuses)->map(fn($s) => (object) $s);

        // Get machines with floor plan coordinates for TV floor plan display
        // Explicit map: "Factory 2" → "f2", "Factory 3 & 4" → "f34"
        $factoryCode = match (true) {
            str_contains($factory, '3') && str_contains($factory, '4') => 'f34',
            str_contains($factory, '2') => 'f2',
            default => strtolower(preg_replace('/[^a-z0-9]/i', '', str_replace('Factory ', 'f', $factory))),
        };
        $machinesWithCoordinates = Machine::where('sc_id', $scId)
            ->where('factory', $factory)
            ->whereNotNull('floor_cx')
            ->whereNotNull('floor_cy')
            ->get();

        $factories = $this->factoryConfig->getFactoryObjects();

        if (!empty($allowedFactories)) {
            $factories = $factories->whereIn('name', $allowedFactories);
        }

        // TOTAL MP for TV
        $total_mp = Member::where('sc_id', $scId)
            ->where('factory', $factory)
            ->whereIn('shift', [$shift, 'AB'])
            ->where('status', 'active')->count();

        $total_mc = Machine::where('sc_id', $scId)
            ->where('factory', $factory)
            ->where('status', 'mesin')
            ->count();

        // Render view terpisah  - standalone HTML, tidak extend layouts.admin
        return view('admin.tv', compact(
            'factory',
            'shift',
            'tanggal',
            'machineSummary',
            'absenceSummary',
            'openLogsCount',
            'statusLevel',
            'groups',
            'statuses',
            'members',
            'machinePhotos',
            'machinesWithCoordinates',
            'factoryCode',
            'factories',
            'factoryDetails',
            'total_mp',
            'total_mc'
        ));
    }

    // =========================================================================
    //  API
    // =========================================================================

    public function statusApi(Request $request)
    {
        $user = Auth::user();
        $scId = ScContext::id();
        $allowedFactories = (!$user->isSuperAdmin() && !empty($user->factory)) ? (array) $user->factory : [];

        // Status API: resolve from query-string first (TV real-time polling), then session.
        $apiSessionFactory = $request->session()->get('factory');
        if (is_array($apiSessionFactory)) {
            $apiSessionFactory = !empty($apiSessionFactory) ? $apiSessionFactory[0] : null;
        }
        $apiRequestedFactory = $request->get('factory');
        if (is_array($apiRequestedFactory)) {
            $apiRequestedFactory = !empty($apiRequestedFactory) ? $apiRequestedFactory[0] : null;
        }
        $factory = ScContext::resolveRequestFactory($apiRequestedFactory, $apiSessionFactory, $user);

        $shift = $request->get('shift', $request->session()->get('shift', 'A'));
        $tanggal = $request->get('tanggal', today()->toDateString());

        $factoryObj = Factory::where('sc_id', $scId)->where('name', $factory)->first();
        $factoryDetails = $factoryObj?->detail_departemen;

        $machineStatuses = $this->getMachineStatuses($tanggal, $factory, $shift);
        $machineSummary = $this->buildSummary($machineStatuses, $tanggal, $factory, $shift);

        $absenceSummary = AbsenceSummary::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
        ])->first();

        $openLogsCount = ProblemLog::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'open',
        ])->whereIn('jenis', ['Machine', 'Material', 'Method'])->count();

        // total_absen = real-time count orang absen (sama dengan man di summary)
        $totalAbsen = $machineSummary['man'];
        // Status level berbasis KY limit vs Total Absen (Task 1 overhaul)
        $kyTotalCount = $this->calcKyTotalCount($factory, $shift);
        $statusLevel = $this->calcStatusLevel(
            $totalAbsen,
            $kyTotalCount,
            $openLogsCount
        );

        // Overdue overlay is now computed CLIENT-SIDE from active_problems.
        // We keep opened_at on each log entry — the TV JS watches for >4h entries
        // scoped to the current factory+shift (no separate server query needed).

        // TOTAL MP: counts active members only — NOT affected by absence/attendance data
        $total_mp = Member::where('sc_id', $scId)
            ->where('factory', $factory)
            ->whereIn('shift', [$shift, 'AB'])
            ->where('status', 'active')->count();

        // Live Announcements Array Logic
        $announcements = [];

        $absenRecordsApi = AbsenceRecord::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'absen',
        ])->get();

        $activeProblems = [];

        foreach ($absenRecordsApi as $recordApi) {
            $memberObj = Member::where('sc_id', $scId)->find($recordApi->member_id);
            if ($memberObj) {
                $memberMesin = $memberObj->mesin ?? 'Tidak diketahui';
                $reason = strtolower($recordApi->reason ?? '');
                $absenLabel = 'Absen';
                if (str_contains($reason, 'sakit'))
                    $absenLabel = 'SAKIT';
                elseif (str_contains($reason, 'izin') || str_contains($reason, 'ijin'))
                    $absenLabel = 'IZIN';
                elseif (str_contains($reason, 'cuti'))
                    $absenLabel = 'CUTI';

                $announcements[] = "👷 <b>{$memberObj->nama}</b> ({$memberMesin}) tidak masuk karena <b>{$absenLabel}</b>";

                $activeProblems[] = [
                    'jenis' => 'Man',
                    'lokasi' => $memberMesin,
                    'deskripsi' => "Absen: {$memberObj->nama} karena " . ucfirst($absenLabel),
                    'cause' => '',
                    'countermeasure' => '',
                    'pic' => '',
                    'waktu_mulai' => null,
                    'waktu_selesai' => null,
                    'durasi' => '-',
                    'tanggal' => $tanggal,
                    'status' => 'open',
                    '_source' => 'absen',
                    'member_id' => $recordApi->member_id
                ];
            }
        }

        $openLogsApi = ProblemLog::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'open',
        ])->whereIn('jenis', ['Machine', 'Material', 'Method'])->get();

        foreach ($openLogsApi as $logApi) {
            $jenis = strtoupper($logApi->jenis ?? 'Problem');
            $announcements[] = "⚠️ Problem <b>{$jenis}</b> terdeteksi pada <b>{$logApi->lokasi}</b>";

            $activeProblems[] = [
                'jenis' => $logApi->jenis,
                'lokasi' => $logApi->lokasi,
                'deskripsi' => $logApi->deskripsi,
                'cause' => $logApi->cause,
                'countermeasure' => $logApi->countermeasure,
                'pic' => $logApi->pic,
                'waktu_mulai' => $logApi->waktu_mulai,
                'waktu_selesai' => $logApi->waktu_selesai,
                'durasi' => $logApi->durasi,
                'tanggal' => $logApi->tanggal,
                'status' => 'open',
                '_source' => 'log',
                'opened_at' => optional($logApi->created_at)->toIso8601String(), // TV overlay uses this
            ];
        }

        return response()->json([
            'total_absen' => $totalAbsen,
            'ky_absent' => $kyTotalCount, // keeping the key name for compatibility if needed, but it's now kyTotalCount
            'problem_mc' => $machineSummary['problem'],
            'open_logs' => $openLogsCount,
            'status_level' => $statusLevel,
            'summary' => $machineSummary,
            'absence' => $absenceSummary,
            'total_mp' => $total_mp,
            'announcements' => $announcements,
            'active_problems' => $activeProblems, // each MC/MM/MT log has `opened_at` for TV watcher
            'updated_at' => now()->format('H:i:s'),
        ]);
    }

    public function setContext(Request $request)
    {
        $request->validate(['factory' => 'required|string', 'shift' => 'required|in:A,B']);

        $user = Auth::user();
        $allowedFactories = (!$user->isSuperAdmin() && !empty($user->factory)) ? (array) $user->factory : [];

        if (!empty($allowedFactories) && !in_array($request->factory, $allowedFactories)) {
            return response()->json(['error' => 'Unauthorized factory scope'], 403);
        }

        $request->session()->put('factory', $request->factory);
        $request->session()->put('shift', $request->shift);
        if ($request->wantsJson())
            return response()->json(['ok' => true]);
        return back();
    }

    /**
     * Set the active SC context for SuperAdmin session-based switching.
     * POST /admin/set-sc
     * Body: { sc_id: int }
     */
    public function setScContext(Request $request)
    {
        $user = Auth::user();

        // Only Super Admins may switch SC context
        if (!$user->isSuperAdmin()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $request->validate(['sc_id' => 'required|integer|exists:scs,id']);

        $request->session()->put('active_sc_id', (int) $request->sc_id);

        // Also reset the factory session so dashboard re-resolves to the new SC's first factory
        $request->session()->forget('factory');

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'active_sc_id' => (int) $request->sc_id]);
        }
        return redirect()->route('admin.dashboard');
    }

    public function tvPicker()
    {
        $user = Auth::user();
        $allowedFactories = (!$user->isSuperAdmin() && !empty($user->factory)) ? (array) $user->factory : [];

        $factories = $this->factoryConfig->getFactoryObjects();

        if (!empty($allowedFactories)) {
            $factories = $factories->whereIn('name', $allowedFactories);
        }

        return view('admin.tv_picker', compact('factories'));
    }

    // =========================================================================
    //  PRIVATE HELPERS
    // =========================================================================

    /**
     * ══ SINGLE SOURCE OF TRUTH  - status visual per mesin  =>
     * develop by rizky
     * Dipakai untuk: border card, pip dots, status pills, dot kecil.
     *
     * Aturan 'statuses[]' (untuk visual card & border):
     *   MAN      → absen DAN belum ada pengganti  (menentukan border merah & needsFinder)
     *   MACHINE  → ada open log 'Machine'
     *   MATERIAL → ada open log 'Material'
     *   METHOD   → ada open log 'Method'
     *
     * Catatan: pip 'man' di foto tetap muncul untuk SEMUA absen
     * (termasuk yang sudah digantikan), diurus di blade via $hasAbsen.
     */
    private function getMachineStatuses(string $tanggal, string $factory, string $shift): array
    {
        $scId = ScContext::id();
        $replacedMesinList = AssignmentReplacement::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
        ])->pluck('target_machine')->toArray();

        $openLogsByMachine = ProblemLog::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'open',
        ])->whereIn('jenis', ['Machine', 'Material', 'Method'])
            ->get()
            ->groupBy('lokasi')
            ->map(fn($logs) => $logs->pluck('jenis')
                ->map(fn($j) => strtolower($j))
                ->unique()->values()->toArray());

        $absenMemberIds = AbsenceRecord::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'absen',
        ])->pluck('member_id')->toArray();

        $absenMesinSet = collect();
        if (!empty($absenMemberIds)) {
            $absenMembers = Member::where('sc_id', $scId)
                ->whereIn('id', $absenMemberIds)
                ->whereNotNull('mesin')
                ->get();

            // Collect both primary and secondary machines from absent members
            foreach ($absenMembers as $member) {
                if ($member->mesin) {
                    $absenMesinSet->push($member->mesin);
                }
                if ($member->mesin_secondary) {
                    $absenMesinSet->push($member->mesin_secondary);
                }
            }
        }

        $result = [];
        //   Gunakan semua mesin dari DB (bukan hardcoded)  
        foreach (Machine::where('sc_id', $scId)->where('factory', $factory)->pluck('name') as $machineName) {
            $active = [];

            // MAN di statuses[] hanya untuk yang belum ada pengganti
            if ($absenMesinSet->contains($machineName) && !in_array($machineName, $replacedMesinList)) {
                $active[] = 'man';
            }

            $logTypes = $openLogsByMachine[$machineName] ?? [];
            if (in_array('machine', $logTypes))
                $active[] = 'machine';
            if (in_array('material', $logTypes))
                $active[] = 'material';
            if (in_array('method', $logTypes))
                $active[] = 'method';

            if (!empty($active)) {
                $result[$machineName] = [
                    'status' => $active[0],
                    'statuses' => $active,
                ];
            }
        }

        return $result;
    }

    /**
     * ══ BUILD SUMMARY => =>
     * fixed by rizky
     * Aturan counter summary:
     *
     *   MAN      → Jumlah ORANG yang absen (tidak hadir) pada shift tsb.
     *              Tidak bergantung pada mesin, tidak turun jika ada pengganti.
     *              Sumber: AbsenceRecord dengan status = 'absen'.
     *
     *   MACHINE  → Jumlah LAPORAN open dengan jenis 'Machine'.
     *              Berkurang ketika laporan ditutup (status → closed).
     *   MATERIAL → Jumlah LAPORAN open dengan jenis 'Material'.
     *              Berkurang ketika laporan ditutup (status → closed).
     *   METHOD   → Jumlah LAPORAN open dengan jenis 'Method'.
     *              Berkurang ketika laporan ditutup (status → closed).
     *
     *   NORMAL   → mesin tanpa masalah apapun
     *   PROBLEM  → machine + material + method (tidak termasuk man)
     */
    private function buildSummary(array $machineStatuses, string $tanggal, string $factory, string $shift): array
    {
        $scId = ScContext::id();
        //   Gunakan DB  - hitung HANYA machines dengan status='mesin' (actual production machines)  
        // Exclude: persons (key persons), lainya (support), mc_vibration (monitoring), dan status lainnya
        $machinesForTotal = Machine::where('sc_id', $scId)
            ->where('factory', $factory)
            ->where('status', 'mesin')
            ->pluck('name')
            ->toArray();
        $total = count($machinesForTotal);
        $machinesForSet = array_flip($machinesForTotal);

        //   MAN: hitung JUMLAH ORANG yang absen (bukan per mesin)  
        $man = AbsenceRecord::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'absen',
        ])->count();

        //   MACHINE / MATERIAL / METHOD: hitung per LAPORAN open (bukan per mesin)  
        $openLogCounts = ProblemLog::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'open',
        ])
            ->whereIn('jenis', ['Machine', 'Material', 'Method'])
            ->selectRaw('jenis, COUNT(*) as total')
            ->groupBy('jenis')
            ->pluck('total', 'jenis');

        $machine = (int) ($openLogCounts['Machine'] ?? 0);
        $material = (int) ($openLogCounts['Material'] ?? 0);
        $method = (int) ($openLogCounts['Method'] ?? 0);

        // NORMAL = mesin yang tidak punya masalah (tetap berbasis mesin untuk tampilan kartu)
        $problemSet = [];
        foreach ($machineStatuses as $machineName => $s) {
            if (!isset($machinesForSet[$machineName]))
                continue;
            if (
                in_array('machine', $s['statuses']) ||
                in_array('material', $s['statuses']) ||
                in_array('method', $s['statuses'])
            ) {
                $problemSet[$machineName] = true;
            }
        }

        $problem = $machine + $material + $method;
        $normal = $total - count($problemSet);

        return compact('total', 'normal', 'man', 'machine', 'material', 'method', 'problem');
    }

    /**
     * ══ HITUNG STATUS LEVEL — KY Capacity vs Total Absen (Task 1) ═══════════
     *
     * @param int $totalAbsen jumlah total MP yang absen
     * @param int $kyTotal    jumlah total jabatan KY (kapasitas backup)
     * @param int $activeMC   jumlah ProblemLog dengan status open
     */
    private function calcStatusLevel(int $totalAbsen, int $kyTotal, int $activeMC): int
    {
        $isAbsenOverLimit = $totalAbsen > $kyTotal;

        if ($isAbsenOverLimit) {
            if ($activeMC === 0)
                return 1; // Ringan
            if ($activeMC === 1)
                return 2; // Khusus
            if ($activeMC >= 2)
                return 3; // Bahaya
        } else {
            // Absen masih dalam limit KY
            if ($activeMC === 0)
                return 0; // Normal
            if ($activeMC === 1)
                return 2; // Khusus (Machine problem is critical)
            if ($activeMC >= 2)
                return 3; // Bahaya
        }

        return 0; // Fallback
    }

    /**
     * ══ HITUNG KY TOTAL COUNT =>════════════
     * Count how many Key Persons (KY) exist for the given factory and shift
     */
    private function calcKyTotalCount(string $factory, string $shift): int
    {
        $scId = ScContext::id();
        return Member::where('sc_id', $scId)
            ->where('factory', $factory)
            ->whereIn('shift', [$shift, 'AB'])
            ->where('status', 'active')
            ->where(function ($q) {
                $q->where('mesin', 'like', 'KY%')
                    ->orWhere('mesin', 'like', 'ky%');
            })
            ->count();
    }
}
