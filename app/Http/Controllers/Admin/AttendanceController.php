<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsenceSummary;
use App\Services\ScContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Attendance
 * 
 * APIs for managing Attendance.
 */
class AttendanceController extends Controller
{
    /**
     * Halaman absensi
     * Menggantikan: page-attendance + loadAbsenFromMemberMgmt()
     * Fixed by Rizky
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $scId = ScContext::id();
        $factory = $request->session()->get('factory', ScContext::firstFactory());
        $shift = $request->session()->get('shift', 'A');
        $tanggal = $request->get('tanggal', today()->toDateString());

        $summary = AbsenceSummary::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
        ])->first() ?? new AbsenceSummary([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
        ]);

        return view('admin.attendance', compact('summary', 'factory', 'shift', 'tanggal'));
    }

    /**
     * Simpan atau update data absensi
     * Menggantikan: saveAbsenSummary() JS
     * POST /admin/attendance
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $scId = ScContext::id();

        $request->validate([
            'tanggal' => 'required|date',
            'factory' => 'required|string',
            'shift' => 'required|in:A,B',
            'mp_hadir' => 'required|integer|min:0',
            'mp_absen' => 'required|integer|min:0',
            'p_cuti' => 'required|integer|min:0',
            'p_sakit' => 'required|integer|min:0',
            'p_ijin' => 'required|integer|min:0',
            'o_cuti' => 'required|integer|min:0',
            'o_sakit' => 'required|integer|min:0',
            'o_ijin' => 'required|integer|min:0',
        ]);

        $totalAbsen = $request->mp_absen
            + $request->p_cuti + $request->p_sakit + $request->p_ijin
            + $request->o_cuti + $request->o_sakit + $request->o_ijin;
        $totalMember = $request->mp_hadir + $totalAbsen;

        $summary = AbsenceSummary::updateOrCreate(
            [
                'sc_id' => $scId,
                'tanggal' => $request->tanggal,
                'factory' => $request->factory,
                'shift' => $request->shift,
            ],
            [
                'mp_hadir' => $request->mp_hadir,
                'mp_absen' => $request->mp_absen,
                'p_cuti' => $request->p_cuti,
                'p_sakit' => $request->p_sakit,
                'p_ijin' => $request->p_ijin,
                'o_cuti' => $request->o_cuti,
                'o_sakit' => $request->o_sakit,
                'o_ijin' => $request->o_ijin,
                'total_absen' => $totalAbsen,
                'total_member' => $totalMember,
                'source' => 'admin_board',
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'summary' => $summary,
                'chart' => $this->buildChartData($summary),
            ]);
        }

        return back()->with('success', '✅ Data absen tersimpan!');
    }

    /**
     * Ambil data absen + data chart untuk AJAX auto-refresh
     * GET /admin/attendance/data?tanggal=&factory=&shift=
     */
    public function getData(Request $request)
    {
        $user = Auth::user();
        $scId = ScContext::id();

        $summary = AbsenceSummary::where([
            'sc_id' => $scId,
            'tanggal' => $request->tanggal,
            'factory' => $request->factory,
            'shift' => $request->shift,
        ])->first();

        return response()->json([
            'summary' => $summary,
            'chart' => $summary ? $this->buildChartData($summary) : null,
        ]);
    }

    // ─── Helper ──────────────────────────────────────────────────────

    /**
     * Build data untuk Chart.js doughnut
     * Menggantikan: buatDiagram() JS  - hanya data-nya, render tetap di JS
     */
    private function buildChartData(AbsenceSummary $summary): array
    {
        return [
            'values' => [
                $summary->mp_hadir,
                $summary->mp_absen,
                $summary->p_cuti,
                $summary->p_sakit,
                $summary->p_ijin,
                $summary->o_cuti,
                $summary->o_sakit,
                $summary->o_ijin,
            ],
            'labels' => ['MP Hadir', 'MP Absen', 'SPV Cuti', 'SPV Sakit', 'SPV Ijin', 'OP Cuti', 'OP Sakit', 'OP Ijin'],
            'colors' => ['#729E3F', '#e74c3c', '#1F3C88', '#5dade2', '#FF8F1F', '#ff69b4', '#8e44ad', '#f1c40f'],
            'total' => $summary->total_member,
            'hadir' => $summary->mp_hadir,
            'pct' => $summary->total_member > 0
                ? round($summary->mp_hadir / $summary->total_member * 100, 1)
                : 0,
        ];
    }
}
