<?php
// ═══════════════════════════════════════════════════════════════════════════
// FILE: app/Http/Controllers/Admin/ReportController.php
// @rizky
// PERUBAHAN:
//   exportExcel()  - full report (Absen + Problem Log + Dashboard) ke XLSX
// ═══════════════════════════════════════════════════════════════════════════

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsenceSummary;
use App\Models\AbsenceRecord;
use App\Models\AssignmentReplacement;
use App\Models\Member;
use App\Models\ProblemLog;
use App\Models\Factory;
use App\Services\ExcelExportService;
use App\Services\FactoryConfigService;
use App\Services\ScContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Report
 * 
 * APIs for managing Report.
 */
class ReportController extends Controller
{
    public function __construct(
        protected FactoryConfigService $factoryConfig,
        protected ExcelExportService $excelService,
    ) {
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $scId = ScContext::id();
        $factories = Factory::where('sc_id', $scId)->orderBy('order_index')->get();

        if (!$user->isSuperAdmin()) {
            $allowedFactories = (array) $user->factory;
            $factories = $factories->filter(fn($f) => in_array($f->name, $allowedFactories));
        }

        $defaultFactory = $factories->first()?->name ?? ScContext::firstFactory() ?? '';
        $factory = $request->session()->get('factory', $defaultFactory);

        // Validate requested factory against allowed scope
        if (!$user->isSuperAdmin()) {
            $allowedFactories = (array) $user->factory;
            if (!in_array($factory, $allowedFactories)) {
                $factory = $defaultFactory;
            }
        }

        $currentFactory = $factories->firstWhere('name', $factory) ?? $factories->first();
        $shift = $request->session()->get('shift', 'A');
        $jenisList = $this->getDynamicJenis();

        // ── Date mode routing ─────────────────────────────────────────
        $mode = $request->get('mode', 'hari');   // hari | bulan | rentang
        $tanggal = $request->get('tanggal', today()->toDateString());

        // Derive date range based on mode
        if ($mode === 'bulan') {
            $bulan = $request->get('bulan', today()->format('Y-m'));
            $dari = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->startOfMonth()->toDateString();
            $sampai = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->endOfMonth()->toDateString();
            $tanggal = $dari; // keep compat  - used in single-day sections
        } elseif ($mode === 'rentang') {
            $dari = $request->get('dari', $tanggal);
            $sampai = $request->get('sampai', $tanggal);
            // ensure dari <= sampai
            if ($dari > $sampai) {
                [$dari, $sampai] = [$sampai, $dari];
            }
            $tanggal = $dari;
            $bulan = \Carbon\Carbon::parse($tanggal)->format('Y-m');
        } else {
            // mode === 'hari'  - single day (default legacy behaviour)
            $mode = 'hari';
            $dari = $tanggal;
            $sampai = $tanggal;
            $bulan = \Carbon\Carbon::parse($tanggal)->format('Y-m');
        }

        $isRange = ($dari !== $sampai); // true for month/rentang multi-day

        // ── Problem Logs (supports range) ─────────────────────────────
        $logsQuery = ProblemLog::where('sc_id', $scId)
            ->where('factory', $factory)
            ->where('shift', $shift)
            ->whereIn('jenis', $jenisList)
            ->whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai');

        $logs = $logsQuery->get();

        // ── Absences (for display  - use first day or aggregate) ───────
        $absenceSummary = AbsenceSummary::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
        ])->first();

        $absenMembers = $isRange
            ? $this->getAbsenDetailRange($dari, $sampai, $factory, $shift)
            : $this->getAbsenDetail($tanggal, $factory, $shift);

        $replacements = AssignmentReplacement::where('sc_id', $scId)
            ->where('factory', $factory)
            ->where('shift', $shift)
            ->whereBetween('tanggal', [$dari, $sampai])
            ->with('member')
            ->get();

        $absenTanpaRepl = $this->getAbsenTanpaPenggantiDetail($absenMembers, $replacements);
        $mesinList = $this->factoryConfig->getAllMachines($factory);

        return view('admin.report', compact(
            'factories',
            'currentFactory',
            'jenisList',
            'factory',
            'shift',
            'tanggal',
            'logs',
            'absenceSummary',
            'absenMembers',
            'replacements',
            'absenTanpaRepl',
            'mesinList',
            'mode',
            'dari',
            'sampai',
            'bulan',
            'isRange'
        ));
    }

    // ── GET /admin/reports/export  (CSV) ──────────────────────────────
    public function exportExcel(Request $request)
    {
        $user = Auth::user();
        $scId = ScContext::id();
        $factories = Factory::where('sc_id', $scId)->orderBy('order_index')->get();

        if (!$user->isSuperAdmin()) {
            $allowedFactories = (array) $user->factory;
            $factories = $factories->filter(fn($f) => in_array($f->name, $allowedFactories));
        }

        $defaultFactory = $factories->first()?->name ?? ScContext::firstFactory() ?? '';
        $factory = $request->get('factory', $request->session()->get('factory', $defaultFactory));

        // Validate requested factory against allowed scope
        if (!$user->isSuperAdmin()) {
            $allowedFactories = (array) $user->factory;
            if (!in_array($factory, $allowedFactories)) {
                $factory = $defaultFactory;
            }
        }

        $shift = $request->get('shift', $request->session()->get('shift', 'A'));

        $mode = $request->get('mode', 'hari');
        $tanggal = $request->get('tanggal', today()->toDateString());

        if ($mode === 'bulan') {
            $bulan = $request->get('bulan', today()->format('Y-m'));
            $dari = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->startOfMonth()->toDateString();
            $sampai = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->endOfMonth()->toDateString();
            $label = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->locale('id')->isoFormat('MMMM YYYY');
        } elseif ($mode === 'rentang') {
            $dari = $request->get('dari', $tanggal);
            $sampai = $request->get('sampai', $tanggal);
            if ($dari > $sampai) {
                [$dari, $sampai] = [$sampai, $dari];
            }
            $label = \Carbon\Carbon::parse($dari)->locale('id')->isoFormat('D MMM YYYY') . ' - ' . \Carbon\Carbon::parse($sampai)->locale('id')->isoFormat('D MMM YYYY');
        } else {
            $dari = $tanggal;
            $sampai = $tanggal;
            $label = \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM YYYY');
        }

        $jenisList = $this->getDynamicJenis();

        $logs = ProblemLog::where([
            'sc_id' => $scId,
            'factory' => $factory,
            'shift' => $shift,
        ])->whereBetween('tanggal', [$dari, $sampai])
            ->whereIn('jenis', $jenisList)
            ->orderBy('waktu_mulai')->get();

        $nsActiveShift = \App\Services\NonShiftResolver::activeShiftFor($sampai);
        $includeNs = ($nsActiveShift === $shift);
        $shifts = [$shift, 'AB'];
        if ($includeNs) {
            $shifts[] = 'NS';
        }

        $members = Member::where('sc_id', $scId)
            ->where('factory', $factory)
            ->whereIn('shift', $shifts)
            ->where('status', 'active')
            ->orderBy('nama')
            ->get();

        // For absences in a range, we grab only 'absen' records so they aren't overwritten by 'hadir' when keyed by member_id
        $recordsQuery = AbsenceRecord::where([
            'sc_id' => $scId,
            'factory' => $factory,
            'shift' => $shift,
        ])->whereBetween('tanggal', [$dari, $sampai]);

        if ($dari !== $sampai) {
            $recordsQuery->where('status', 'absen');
        }
        $records = $recordsQuery->get()->keyBy('member_id');

        $replacements = AssignmentReplacement::where([
            'sc_id' => $scId,
            'factory' => $factory,
            'shift' => $shift,
        ])->whereBetween('tanggal', [$dari, $sampai])->with('member')->get();

        $path = $this->excelService->buildFullReport(
            $factory,
            $shift,
            $tanggal,
            $members,
            $records,
            $logs,
            $replacements,
            $label // passing formatted label
        );

        $fileNameType = $dari === $sampai ? $tanggal : "{$dari}_to_{$sampai}";
        $filename = "Laporan_{$factory}_Shift{$shift}_{$fileNameType}.xlsx";

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // ── GET /admin/reports/backup  (JSON) ─────────────────────────────
    public function exportJson(Request $request)
    {
        $user = Auth::user();
        $scId = ScContext::id();
        $factories = Factory::where('sc_id', $scId)->orderBy('order_index')->get();

        if (!$user->isSuperAdmin()) {
            $allowedFactories = (array) $user->factory;
            $factories = $factories->filter(fn($f) => in_array($f->name, $allowedFactories));
        }

        $defaultFactory = $factories->first()?->name ?? ScContext::firstFactory() ?? '';
        $factory = $request->get('factory', $request->session()->get('factory', $defaultFactory));

        // Validate requested factory against allowed scope
        if (!$user->isSuperAdmin()) {
            $allowedFactories = (array) $user->factory;
            if (!in_array($factory, $allowedFactories)) {
                $factory = $defaultFactory;
            }
        }

        $shift = $request->get('shift', $request->session()->get('shift', 'A'));

        $mode = $request->get('mode', 'hari');
        $tanggal = $request->get('tanggal', today()->toDateString());

        if ($mode === 'bulan') {
            $bulan = $request->get('bulan', today()->format('Y-m'));
            $dari = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->startOfMonth()->toDateString();
            $sampai = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->endOfMonth()->toDateString();
        } elseif ($mode === 'rentang') {
            $dari = $request->get('dari', $tanggal);
            $sampai = $request->get('sampai', $tanggal);
            if ($dari > $sampai) {
                [$dari, $sampai] = [$sampai, $dari];
            }
        } else {
            $dari = $tanggal;
            $sampai = $tanggal;
        }

        $jenisList = $this->getDynamicJenis();

        $logs = ProblemLog::where(['sc_id' => $scId, 'factory' => $factory, 'shift' => $shift])
            ->whereBetween('tanggal', [$dari, $sampai])
            ->whereIn('jenis', $jenisList)->get();

        // Use the first record if multi-day, or compute average. For simplicity, just get one if exists
        $absenceSummary = AbsenceSummary::where(['sc_id' => $scId, 'factory' => $factory, 'shift' => $shift])
            ->whereBetween('tanggal', [$dari, $sampai])->first();

        $absenMembers = $dari === $sampai
            ? $this->getAbsenDetail($dari, $factory, $shift)
            : $this->getAbsenDetailRange($dari, $sampai, $factory, $shift);

        $replacements = AssignmentReplacement::where(['sc_id' => $scId, 'factory' => $factory, 'shift' => $shift])
            ->whereBetween('tanggal', [$dari, $sampai])->get();

        $payload = [
            'meta' => compact('factory', 'shift', 'mode', 'dari', 'sampai', 'tanggal'),
            'absence_summary' => $absenceSummary,
            'absen_members' => $absenMembers,
            'replacements' => $replacements,
            'problem_logs' => $logs,
            'exported_at' => now()->toIso8601String(),
        ];

        $fileNameType = $dari === $sampai ? $tanggal : "{$dari}_to_{$sampai}";
        $filename = "backup_{$factory}_shift{$shift}_{$fileNameType}.json";
        return response()->json($payload)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    // ─── Helpers ──────────────────────────────────────────────────────

    private function getAbsenDetail(string $tanggal, string $factory, string $shift)
    {
        $scId = ScContext::id();
        $absenMemberIds = AbsenceRecord::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'absen',
        ])->pluck('member_id');

        return Member::where('sc_id', $scId)->whereIn('id', $absenMemberIds)->get();
    }

    private function getAbsenDetailRange(string $dari, string $sampai, string $factory, string $shift)
    {
        $scId = ScContext::id();
        $absenMemberIds = AbsenceRecord::where('sc_id', $scId)
            ->where('factory', $factory)
            ->where('shift', $shift)
            ->where('status', 'absen')
            ->whereBetween('tanggal', [$dari, $sampai])
            ->pluck('member_id')
            ->unique();

        return Member::where('sc_id', $scId)->whereIn('id', $absenMemberIds)->get();
    }

    private function getAbsenTanpaPenggantiDetail($absenMembers, $replacements): array
    {
        $absenMesin = $absenMembers->whereNotNull('mesin')->pluck('mesin')->toArray();
        $replacedMesin = $replacements->pluck('target_machine')->toArray();
        return array_values(array_diff($absenMesin, $replacedMesin));
    }

    private function getDynamicJenis(): array
    {
        try {
            $type = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM problem_logs WHERE Field = 'jenis'")[0]->Type;
            preg_match('/^enum\((.*)\)$/', $type, $matches);
            $types = [];
            foreach (explode(',', $matches[1]) as $value) {
                $types[] = trim($value, "'");
            }
            return $types;
        } catch (\Exception $e) {
            return ['Machine', 'Material', 'Method']; // fallback backward compatibility
        }
    }
}
