<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsenceSummary;
use App\Models\ProblemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    /**
     * Halaman laporan
     * Menggantikan: page-report + generateDetailedReport()
     */
    public function index(Request $request)
    {
        $factory = $request->session()->get('factory', 'Factory 2');
        $shift   = $request->session()->get('shift', 'A');
        $tanggal = $request->get('tanggal', today()->toDateString());

        $logs = ProblemLog::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
        ])->orderBy('waktu_mulai')->get();

        return view('admin.report', compact('logs', 'factory', 'shift', 'tanggal'));
    }

    /**
     * Export ke Excel (CSV sederhana, bisa diganti dengan Laravel Excel)
     * Menggantikan: exportDetailedReport() JS (pakai SheetJS XLSX)
     * GET /admin/reports/export?tanggal=&factory=&shift=
     */
    public function exportExcel(Request $request)
    {
        $factory = $request->get('factory', session('factory', 'Factory 2'));
        $shift   = $request->get('shift', session('shift', 'A'));
        $tanggal = $request->get('tanggal', today()->toDateString());

        $logs = ProblemLog::where([
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift'   => $shift,
        ])->orderBy('waktu_mulai')->get();

        $filename = "HENKATEN_{$factory}_{$tanggal}_Shift{$shift}.csv";
        $filename = str_replace([' ', '&'], ['_', ''], $filename);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $rows = [];
        $rows[] = ['HENKATEN BOARD - PROBLEM REPORT'];
        $rows[] = ["Factory: {$factory}", "Date: {$tanggal}", "Shift: {$shift}", "Generated: " . now()->toDateTimeString()];
        $rows[] = [];
        $rows[] = ['No','Tanggal','Mulai','Selesai','Durasi','Status','Shift','Factory','Mesin','Tipe','Deskripsi','Penyebab','Countermeasure','PIC'];

        foreach ($logs as $i => $log) {
            $rows[] = [
                $i + 1,
                $log->tanggal,
                $log->waktu_mulai,
                $log->waktu_selesai ?? '-',
                $log->durasi ?? ($log->status === 'open' ? 'ON GOING' : '-'),
                strtoupper($log->status),
                $log->shift,
                $log->factory,
                $log->lokasi,
                $log->jenis,
                $log->deskripsi,
                $log->cause ?? '-',
                $log->countermeasure ?? '-',
                $log->pic ?? '-',
            ];
        }

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export data JSON (backup)
     * Menggantikan: exportData() JS
     * GET /admin/reports/backup?tanggal=&factory=&shift=
     */
    public function exportJson(Request $request)
    {
        $factory = $request->get('factory', session('factory', 'Factory 2'));
        $shift   = $request->get('shift', session('shift', 'A'));
        $tanggal = $request->get('tanggal', today()->toDateString());

        $logs    = ProblemLog::where(compact('tanggal','factory','shift'))->orderBy('waktu_mulai')->get();
        $absence = AbsenceSummary::where(compact('tanggal','factory','shift'))->first();

        $data = [
            'metadata' => [
                'factory'     => $factory,
                'shift'       => $shift,
                'date'        => $tanggal,
                'export_time' => now()->toIso8601String(),
                'source'      => 'henkaten-laravel',
            ],
            'absence'  => $absence,
            'logs'     => $logs,
        ];

        $filename = "henkaten-{$factory}-{$tanggal}-S{$shift}.json";
        $filename = str_replace([' ', '&'], ['_', ''], $filename);

        return Response::json($data, 200, [
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
