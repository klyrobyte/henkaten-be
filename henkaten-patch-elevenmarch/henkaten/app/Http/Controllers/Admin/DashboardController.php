<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsenceSummary;
use App\Models\AbsenceRecord;
use App\Models\AssignmentReplacement;
use App\Models\Machine;
use App\Models\Member;
use App\Models\ProblemLog;
use App\Services\FactoryConfigService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected FactoryConfigService $factoryConfig;

    public function __construct(FactoryConfigService $factoryConfig)
    {
        $this->factoryConfig = $factoryConfig;
    }

    // =========================================================================
    //  PUBLIC ROUTES
    // =========================================================================

    public function index(Request $request)
    {
        $factory = $request->session()->get('factory', 'Factory 2');
        $shift = $request->session()->get('shift', 'A');
        $tanggal = $request->get('tanggal', today()->toDateString());

        $machineStatuses = $this->getMachineStatuses($tanggal, $factory, $shift);
        $machineSummary = $this->buildSummary($machineStatuses, $tanggal, $factory, $shift);

        $totMachineF34 = Machine::whereIn('factory', ['Factory 3', 'Factory 4', 'Factory 3 & 4'])->where('status', 'mesin')->count();

        $absenceSummary = AbsenceSummary::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
        ])->first();

        $openLogsCount = ProblemLog::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'open',
        ])->whereIn('jenis', ['Machine', 'Material', 'Method'])->count();

        // Status level kini berbasis totalMan (semua absen) bukan hanya tanpa pengganti
        $statusLevel = $this->calcStatusLevel(
            $machineSummary['man'],
            $machineSummary['problem'],
            $openLogsCount
        );

        $groups = $this->factoryConfig->getGroups($factory);
        $members = Member::where('factory', $factory)
            ->where('shift', $shift)
            ->where('status', 'active')
            ->orderBy('id')->get();

        $machinePhotos = Machine::where('factory', $factory)->get()->keyBy('name');

        $totalMP = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'hadir',
        ])->count();
        if ($totalMP === 0) {
            $totalMP = Member::where('factory', $factory)
                ->whereIn('shift', [$shift, 'AB'])
                ->where('status', 'active')->count();
        }

        $statuses = collect($machineStatuses)->map(fn($s) => (object) $s);

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
            'totalMP'
        ));
    }

    public function statusApi(Request $request)
    {
        $factory = $request->get('factory', $request->session()->get('factory', 'Factory 2'));
        $shift = $request->get('shift', $request->session()->get('shift', 'A'));
        $tanggal = $request->get('tanggal', today()->toDateString());

        $machineStatuses = $this->getMachineStatuses($tanggal, $factory, $shift);
        $machineSummary = $this->buildSummary($machineStatuses, $tanggal, $factory, $shift);

        $absenceSummary = AbsenceSummary::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
        ])->first();

        $openLogsCount = ProblemLog::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'open',
        ])->whereIn('jenis', ['Machine', 'Material', 'Method'])->count();

        $totalAbsen = $absenceSummary?->total_absen ?? 0;
        $statusLevel = $this->calcStatusLevel(
            $machineSummary['man'],
            $machineSummary['problem'],
            $openLogsCount
        );

        $totalMP = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'hadir',
        ])->count();
        if ($totalMP === 0) {
            $totalMP = Member::where('factory', $factory)
                ->whereIn('shift', [$shift, 'AB'])
                ->where('status', 'active')->count();
        }

        $totMachineF34 = Machine::whereIn('factory', ['Factory 3', 'Factory 4', 'Factory 3 & 4'])->where('status', 'mesin')->count();

        return response()->json([
            'total_absen' => $totalAbsen,
            'problem_mc' => $machineSummary['problem'],
            'open_logs' => $openLogsCount,
            'status_level' => $statusLevel,
            'summary' => $machineSummary,
            'absence' => $absenceSummary,
            'total_mp' => $totalMP,
            'total_mesinf34' => $totMachineF34,
            'updated_at' => now()->format('H:i:s'),
        ]);
    }

    public function setContext(Request $request)
    {
        $request->validate(['factory' => 'required|string', 'shift' => 'required|in:A,B']);
        $request->session()->put('factory', $request->factory);
        $request->session()->put('shift', $request->shift);
        if ($request->wantsJson())
            return response()->json(['ok' => true]);
        return back();
    }

    // =========================================================================
    //  PRIVATE HELPERS
    // =========================================================================

    /**
     * ══ SINGLE SOURCE OF TRUTH  - status visual per mesin ════════════════════
     *
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
        $replacedMesinList = AssignmentReplacement::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
        ])->pluck('target_machine')->toArray();

        $openLogsByMachine = ProblemLog::where([
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
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'absen',
        ])->pluck('member_id')->toArray();

        $absenMesinSet = collect();
        if (!empty($absenMemberIds)) {
            $absenMesinSet = Member::whereIn('id', $absenMemberIds)
                ->whereNotNull('mesin')
                ->pluck('mesin');
        }

        $result = [];
        foreach ($this->factoryConfig->getAllMachines($factory) as $machineName) {
            $active = [];

            // MAN di statuses[] hanya untuk yang belum ada pengganti
            // (menentukan border merah & tombol finder)
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
     * ══ BUILD SUMMARY ════════════════════════════════════════════════════════
     *
     * Aturan counter summary (berbeda dari statuses[]):
     *
     *   MAN      → SEMUA mesin yang ada anggota absen, termasuk yang sudah
     *              digantikan. Tidak turun ketika ada pengganti.
     *              (Menggambarkan kondisi aktual: berapa mesin kekurangan orang asli)
     *
     *   MACHINE  → mesin dengan open log 'Machine'  (kembali 0 saat log ditutup)
     *   MATERIAL → mesin dengan open log 'Material' (kembali 0 saat log ditutup)
     *   METHOD   → mesin dengan open log 'Method'   (kembali 0 saat log ditutup)
     *
     *   NORMAL   → mesin tanpa masalah apapun
     *   PROBLEM  → machine + material + method (tidak termasuk man)
     */
    private function buildSummary(array $machineStatuses, string $tanggal, string $factory, string $shift): array
    {
        $machinesForTotal = $this->factoryConfig->getAllMachines($factory, excludeKeyPersons: true);
        $total = count($machinesForTotal);
        $machinesForSet = array_flip($machinesForTotal);

        // ── MAN: hitung semua mesin yang ada absennya (termasuk sudah digantikan) ──
        // Query langsung  - tidak tergantung pada $machineStatuses yang hanya
        // menyimpan 'man' untuk yang belum digantikan.
        $absenMemberIds = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'absen',
        ])->pluck('member_id')->toArray();

        $man = 0;
        if (!empty($absenMemberIds)) {
            // Hitung berapa mesin NON-Key Persons yang ditinggal absen
            $man = Member::whereIn('id', $absenMemberIds)
                ->whereNotNull('mesin')
                ->whereIn('mesin', $machinesForTotal) // hanya non-Key Persons
                ->distinct('mesin')
                ->count('mesin');
        }

        // ── MACHINE / MATERIAL / METHOD: dari $machineStatuses (open log saja) ──
        $machine = $material = $method = 0;
        $problemSet = [];

        foreach ($machineStatuses as $machineName => $s) {
            if (!isset($machinesForSet[$machineName]))
                continue;

            if (in_array('machine', $s['statuses'])) {
                $machine++;
                $problemSet[$machineName] = true;
            }
            if (in_array('material', $s['statuses'])) {
                $material++;
                $problemSet[$machineName] = true;
            }
            if (in_array('method', $s['statuses'])) {
                $method++;
                $problemSet[$machineName] = true;
            }
        }

        $problem = $machine + $material + $method;
        $normal = $total - count($problemSet);

        return compact('total', 'normal', 'man', 'machine', 'material', 'method', 'problem');
    }

    /**
     * ══ HITUNG STATUS LEVEL ══════════════════════════════════════════════════
     *
     * Level menggunakan $totalMan (semua absen, termasuk sudah digantikan):
     *
     *   0 → AMAN    : man = 0, tidak ada problem MC, tidak ada open log
     *   1 → RINGAN  : man = 1
     *   2 → KHUSUS  : man 2–3  ATAU ada problem MC  ATAU ada open log
     *   3 → BAHAYA  : man >= 4 ATAU problem MC >= 2  ATAU open log >= 3
     */
    private function calcStatusLevel(int $totalMan, int $problemMC, int $openLogs): int
    {
        if ($totalMan >= 4 || $problemMC >= 2 || $openLogs >= 3)
            return 3; // BAHAYA
        if ($totalMan >= 2 || $problemMC >= 1 || $openLogs >= 1)
            return 2; // KHUSUS
        if ($totalMan === 1)
            return 1; // RINGAN
        return 0;                                                            // AMAN
    }
}