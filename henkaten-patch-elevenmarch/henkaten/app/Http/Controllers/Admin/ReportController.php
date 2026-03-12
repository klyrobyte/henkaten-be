<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsenceSummary;
use App\Models\AbsenceRecord;
use App\Models\AssignmentReplacement;
use App\Models\Member;
use App\Models\ProblemLog;
use App\Services\FactoryConfigService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(protected FactoryConfigService $factoryConfig) {}

    public function index(Request $request)
    {
        $factory = $request->session()->get('factory', 'Factory 2');
        $shift   = $request->session()->get('shift', 'A');
        $tanggal = $request->get('tanggal', today()->toDateString());

        // Problem logs — HANYA 3M (Machine / Material / Method)
        // Man tidak ada di ProblemLog, diambil dari AbsenceRecord
        $logs = ProblemLog::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
        ])->whereIn('jenis', ['Machine', 'Material', 'Method'])
          ->orderBy('waktu_mulai')
          ->get();

        // Absence summary (untuk chart kehadiran)
        $absenceSummary = AbsenceSummary::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
        ])->first();

        // Absen detail: member yang absen hari ini
        $absenMembers = $this->getAbsenDetail($tanggal, $factory, $shift);

        // Pengganti yang sudah assign
        $replacements = AssignmentReplacement::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
        ])->with('member')->get();

        // Mesin absen tanpa pengganti
        $absenTanpaRepl = $this->getAbsenTanpaPenggantiDetail($absenMembers, $replacements);

        // Daftar mesin untuk dropdown form tambah log
        $mesinList = $this->factoryConfig->getAllMachines($factory);

        return view('admin.report', compact(
            'factory', 'shift', 'tanggal',
            'logs', 'absenceSummary',
            'absenMembers', 'replacements', 'absenTanpaRepl',
            'mesinList'
        ));
    }

    public function exportExcel(Request $request)
    {
        $factory = $request->get('factory', $request->session()->get('factory', 'Factory 2'));
        $shift   = $request->get('shift',   $request->session()->get('shift', 'A'));
        $tanggal = $request->get('tanggal', today()->toDateString());

        $logs = ProblemLog::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
        ])->whereIn('jenis', ['Machine', 'Material', 'Method'])
          ->orderBy('waktu_mulai')
          ->get();

        $absenceSummary = AbsenceSummary::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
        ])->first();

        $absenMembers = $this->getAbsenDetail($tanggal, $factory, $shift);

        $filename = "report_{$factory}_shift{$shift}_{$tanggal}.csv";
        $headers  = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""];

        $callback = function () use ($logs, $absenceSummary, $absenMembers, $factory, $shift, $tanggal) {
            $out = fopen('php://output', 'w');

            // Header info
            fputcsv($out, ['LAPORAN HARIAN - PROBLEM LOG']);
            fputcsv($out, ['Factory', $factory, 'Shift', $shift, 'Tanggal', $tanggal]);
            fputcsv($out, []);

            // Ringkasan absensi
            fputcsv($out, ['=== ABSENSI ===']);
            fputcsv($out, ['Total Member', $absenceSummary?->total_member ?? 0]);
            fputcsv($out, ['MP Hadir',     $absenceSummary?->mp_hadir ?? 0]);
            fputcsv($out, ['MP Absen',     $absenceSummary?->mp_absen ?? 0]);
            fputcsv($out, []);

            // Detail absen
            if ($absenMembers->isNotEmpty()) {
                fputcsv($out, ['Detail Absen']);
                fputcsv($out, ['Nama', 'NIK', 'Mesin', 'Jabatan']);
                foreach ($absenMembers as $m) {
                    fputcsv($out, [$m->nama, $m->nik, $m->mesin ?? '-', $m->jabatan]);
                }
                fputcsv($out, []);
            }

            // Problem log 3M
            fputcsv($out, ['=== PROBLEM LOG (3M) ===']);
            fputcsv($out, ['No','Jenis','Lokasi','Waktu Mulai','Waktu Selesai','Durasi','Status','Deskripsi','Cause','Countermeasure','PIC']);
            foreach ($logs as $i => $log) {
                fputcsv($out, [
                    $i + 1,
                    $log->jenis,
                    $log->lokasi,
                    $log->waktu_mulai,
                    $log->waktu_selesai ?? '',
                    $log->durasi ?? '',
                    strtoupper($log->status),
                    $log->deskripsi,
                    $log->cause ?? '',
                    $log->countermeasure ?? '',
                    $log->pic ?? '',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportJson(Request $request)
    {
        $factory = $request->get('factory', $request->session()->get('factory', 'Factory 2'));
        $shift   = $request->get('shift',   $request->session()->get('shift', 'A'));
        $tanggal = $request->get('tanggal', today()->toDateString());

        $logs = ProblemLog::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
        ])->whereIn('jenis', ['Machine', 'Material', 'Method'])->get();

        $absenceSummary = AbsenceSummary::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
        ])->first();

        $absenMembers = $this->getAbsenDetail($tanggal, $factory, $shift);

        $replacements = AssignmentReplacement::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
        ])->get();

        $payload = [
            'meta'            => compact('factory', 'shift', 'tanggal'),
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

    /**
     * Member yang absen hari ini beserta info mesin mereka.
     */
    private function getAbsenDetail(string $tanggal, string $factory, string $shift)
    {
        $absenMemberIds = AbsenceRecord::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
            'status'  => 'absen',
        ])->pluck('member_id');

        return Member::whereIn('id', $absenMemberIds)->get();
    }

    /**
     * Dari daftar absen & replacements, kembalikan nama mesin
     * yang belum punya pengganti (untuk tampil di laporan).
     */
    private function getAbsenTanpaPenggantiDetail($absenMembers, $replacements): array
    {
        $absenMesin    = $absenMembers->whereNotNull('mesin')->pluck('mesin')->toArray();
        $replacedMesin = $replacements->pluck('target_machine')->toArray();

        return array_values(array_diff($absenMesin, $replacedMesin));
    }
}