<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsenceSummary;
use App\Models\MachineStatus;
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

    public function index(Request $request)
    {
        $factory = $request->session()->get('factory', 'Factory 2');
        $shift   = $request->session()->get('shift', 'A');
        $tanggal = $request->get('tanggal', today()->toDateString());

        $machineSummary = $this->getMachineSummary($tanggal, $factory, $shift);
        $absenceSummary = AbsenceSummary::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
        ])->first();

        $openLogsCount = ProblemLog::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
            'status'  => 'open',
        ])->count();

        $totalAbsen  = $absenceSummary?->total_absen ?? 0;
        $problemMC   = $machineSummary['problem'];
        $statusLevel = $this->calcStatusLevel($totalAbsen, $problemMC, $openLogsCount);

        return view('admin.dashboard', compact(
            'factory', 'shift', 'tanggal',
            'machineSummary', 'absenceSummary',
            'openLogsCount', 'statusLevel'
        ));
    }

    /**
     * API: status polling — dipakai dashboard auto-refresh DAN mesin.blade loadLights
     */
    public function statusApi(Request $request)
    {
        $factory = $request->get('factory', $request->session()->get('factory', 'Factory 2'));
        $shift   = $request->get('shift',   $request->session()->get('shift', 'A'));
        $tanggal = $request->get('tanggal', today()->toDateString());

        $absenceSummary = AbsenceSummary::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
        ])->first();

        $machineSummary = $this->getMachineSummary($tanggal, $factory, $shift);

        $openLogsCount = ProblemLog::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
            'status'  => 'open',
        ])->count();

        $totalAbsen  = $absenceSummary?->total_absen ?? 0;
        $statusLevel = $this->calcStatusLevel($totalAbsen, $machineSummary['problem'], $openLogsCount);

        return response()->json([
            'total_absen'  => $totalAbsen,
            'problem_mc'   => $machineSummary['problem'],
            'open_logs'    => $openLogsCount,
            'status_level' => $statusLevel,
            'summary'      => $machineSummary,
            'absence'      => $absenceSummary,
            'updated_at'   => now()->format('H:i:s'),
        ]);
    }

    public function setContext(Request $request)
    {
        $request->validate([
            'factory' => 'required|string',
            'shift'   => 'required|in:A,B',
        ]);

        $request->session()->put('factory', $request->factory);
        $request->session()->put('shift',   $request->shift);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }
        return back();
    }

    // ─── Private helpers ──────────────────────────────────────────────

    /**
     * FIX: Hitung summary mesin — gabungkan MachineStatus DAN open ProblemLog
     * Sebelumnya hanya dari MachineStatus, sehingga log masalah tidak terhitung
     */
    private function getMachineSummary(string $tanggal, string $factory, string $shift): array
    {
        $allMachines = $this->factoryConfig->getAllMachines($factory, excludeKeyPersons: true);
        $total       = count($allMachines);

        // Sumber 1: MachineStatus (set manual dari card mesin)
        $statuses = MachineStatus::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
        ])->whereIn('machine_name', $allMachines)
          ->get()
          ->keyBy('machine_name');

        // Sumber 2: Open ProblemLog — mesin dengan log open dianggap bermasalah
        // Grup per lokasi mesin, ambil jenis masalah unik
        $openLogs = ProblemLog::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
            'status'  => 'open',
        ])->whereIn('lokasi', $allMachines)
          ->get()
          ->groupBy('lokasi');

        // Gabungkan: tiap mesin bisa punya status dari MachineStatus atau dari log
        $man = $machine = $method = $material = 0;
        $problemMachines = []; // tracking mesin yang sudah dihitung agar tidak dobel

        foreach ($allMachines as $m) {
            $stVal = $statuses[$m]?->status ?? 'normal';

            // Jika ada open log untuk mesin ini, override/tambahkan ke count
            if (isset($openLogs[$m])) {
                $logTypes = $openLogs[$m]->pluck('jenis')->unique();
                // Ambil jenis masalah pertama dari log jika status masih normal
                if ($stVal === 'normal') {
                    $stVal = strtolower($logTypes->first() ?? 'normal');
                }
            }

            if ($stVal === 'normal') continue;
            if (in_array($m, $problemMachines)) continue;
            $problemMachines[] = $m;

            match ($stVal) {
                'man'      => $man++,
                'machine'  => $machine++,
                'method'   => $method++,
                'material' => $material++,
                default    => null,
            };
        }

        $problem = $man + $machine + $method + $material;
        return compact('total', 'man', 'machine', 'method', 'material', 'problem');
    }

    private function calcStatusLevel(int $absen, int $problemMC, int $openLogs): int
    {
        if ($absen >= 4 || $problemMC >= 2 || $openLogs >= 3) return 3;
        if ($absen >= 2 || $problemMC >= 1 || $openLogs >= 1) return 2;
        if ($absen === 1) return 1;
        return 0;
    }
}