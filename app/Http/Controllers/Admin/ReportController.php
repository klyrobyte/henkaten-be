<?php
// ═══════════════════════════════════════════════════════════════════════════
// FILE: app/Http/Controllers/Admin/ReportController.php
//
// PERUBAHAN:
//   exportExcel() — full report (Absen + Problem Log + Dashboard) ke XLSX
// ═══════════════════════════════════════════════════════════════════════════

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsenceSummary;
use App\Models\AbsenceRecord;
use App\Models\AssignmentReplacement;
use App\Models\Member;
use App\Models\ProblemLog;
use App\Services\ExcelExportService;
use App\Services\FactoryConfigService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected FactoryConfigService $factoryConfig,
        protected ExcelExportService   $excelService,
    ) {}

    public function index(Request $request)
    {
        $factory = $request->session()->get('factory', 'Factory 2');
        $shift   = $request->session()->get('shift', 'A');
        $tanggal = $request->get('tanggal', today()->toDateString());

        $logs = ProblemLog::where([
            'tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift,
        ])->whereIn('jenis', ['Machine','Material','Method'])->orderBy('waktu_mulai')->get();

        $absenceSummary = AbsenceSummary::where([
            'tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift,
        ])->first();

        $absenMembers   = $this->getAbsenDetail($tanggal, $factory, $shift);
        $replacements   = AssignmentReplacement::where([
            'tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift,
        ])->with('member')->get();
        $absenTanpaRepl = $this->getAbsenTanpaPenggantiDetail($absenMembers, $replacements);
        $mesinList      = $this->factoryConfig->getAllMachines($factory);

        return view('admin.report', compact(
            'factory','shift','tanggal',
            'logs','absenceSummary',
            'absenMembers','replacements','absenTanpaRepl',
            'mesinList'
        ));
    }

    // ── GET /admin/reports/export  (CSV) ──────────────────────────────
    public function exportExcel(Request $request)
    {
        $factory = $request->get('factory', $request->session()->get('factory', 'Factory 2'));
        $shift   = $request->get('shift',   $request->session()->get('shift', 'A'));
        $tanggal = $request->get('tanggal', today()->toDateString());

        $logs = ProblemLog::where([
            'tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift,
        ])->whereIn('jenis', ['Machine','Material','Method'])->orderBy('waktu_mulai')->get();

        $members = Member::where('factory', $factory)
            ->whereIn('shift', [$shift, 'AB'])
            ->where('status', 'active')
            ->orderBy('nama')
            ->get();

        $records = AbsenceRecord::where([
            'tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift,
        ])->get()->keyBy('member_id');

        $replacements = AssignmentReplacement::where([
            'tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift,
        ])->with('member')->get();

        $path = $this->excelService->buildFullReport(
            $factory, $shift, $tanggal,
            $members, $records, $logs, $replacements
        );

        $filename = "LaporanHarian_{$factory}_Shift{$shift}_{$tanggal}.xlsx";

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // ── GET /admin/reports/backup  (JSON) ─────────────────────────────
    public function exportJson(Request $request)
    {
        $factory = $request->get('factory', $request->session()->get('factory', 'Factory 2'));
        $shift   = $request->get('shift',   $request->session()->get('shift', 'A'));
        $tanggal = $request->get('tanggal', today()->toDateString());

        $logs           = ProblemLog::where(['tanggal'=>$tanggal,'factory'=>$factory,'shift'=>$shift])
                            ->whereIn('jenis',['Machine','Material','Method'])->get();
        $absenceSummary = AbsenceSummary::where(['tanggal'=>$tanggal,'factory'=>$factory,'shift'=>$shift])->first();
        $absenMembers   = $this->getAbsenDetail($tanggal, $factory, $shift);
        $replacements   = AssignmentReplacement::where(['tanggal'=>$tanggal,'factory'=>$factory,'shift'=>$shift])->get();

        $payload = [
            'meta'            => compact('factory','shift','tanggal'),
            'absence_summary' => $absenceSummary,
            'absen_members'   => $absenMembers,
            'replacements'    => $replacements,
            'problem_logs'    => $logs,
            'exported_at'     => now()->toIso8601String(),
        ];

        $filename = "backup_{$factory}_shift{$shift}_{$tanggal}.json";
        return response()->json($payload)
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    // ─── Helpers ──────────────────────────────────────────────────────

    private function getAbsenDetail(string $tanggal, string $factory, string $shift)
    {
        $absenMemberIds = AbsenceRecord::where([
            'tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift, 'status' => 'absen',
        ])->pluck('member_id');

        return Member::whereIn('id', $absenMemberIds)->get();
    }

    private function getAbsenTanpaPenggantiDetail($absenMembers, $replacements): array
    {
        $absenMesin    = $absenMembers->whereNotNull('mesin')->pluck('mesin')->toArray();
        $replacedMesin = $replacements->pluck('target_machine')->toArray();
        return array_values(array_diff($absenMesin, $replacedMesin));
    }
}