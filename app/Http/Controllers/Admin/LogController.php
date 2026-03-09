<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProblemLog;
use App\Services\FactoryConfigService;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function __construct(protected FactoryConfigService $factoryConfig) {}

    /**
     * Halaman problem log
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
        ])->latest()->get();

        $mesinList = $this->factoryConfig->getAllMachines($factory);

        return view('admin.log', compact('logs', 'mesinList', 'factory', 'shift', 'tanggal'));
    }

    /**
     * Simpan log baru
     * POST /admin/logs
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'        => 'required|date',
            'factory'        => 'required|string',
            'shift'          => 'required|in:A,B',
            'jenis'          => 'required|in:Man,Material,Machine,Method',
            'lokasi'         => 'required|string',
            'waktu_mulai'    => 'required|date_format:H:i',
            'waktu_selesai'  => 'nullable|date_format:H:i',
            'status'         => 'required|in:open,closed',
            'deskripsi'      => 'required|string',
            'cause'          => 'nullable|string',
            'countermeasure' => 'nullable|string',
            'pic'            => 'nullable|string',
        ]);

        $durasi = null;
        if ($request->filled('waktu_selesai')) {
            $durasi = $this->calcDuration($request->waktu_mulai, $request->waktu_selesai);
        }

        // FIX: created_by harus integer atau null
        // auth()->id() bisa return string ('admin') jika pakai custom session auth
        // Paksa null agar tidak crash FK constraint
        $rawId     = auth()->id();
        $createdBy = is_numeric($rawId) ? (int) $rawId : null;

        $log = ProblemLog::create([
            'tanggal'        => $request->tanggal,
            'factory'        => $request->factory,
            'shift'          => $request->shift,
            'jenis'          => $request->jenis,
            'lokasi'         => $request->lokasi,
            'waktu_mulai'    => $request->waktu_mulai,
            'waktu_selesai'  => $request->waktu_selesai ?: null,
            'status'         => $request->status,
            'deskripsi'      => $request->deskripsi,
            'cause'          => $request->cause ?: null,
            'countermeasure' => $request->countermeasure ?: null,
            'pic'            => $request->pic ?: null,
            'durasi'         => $durasi,
            'created_by'     => $createdBy,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'log' => $log]);
        }

        return back()->with('success', "Log ditambah: {$request->jenis} - {$request->lokasi}");
    }

    /**
     * Tutup log
     * PATCH /admin/logs/{id}/close
     */
    public function close(ProblemLog $log)
    {
        $waktuSelesai = now()->format('H:i');
        $log->update([
            'waktu_selesai' => $waktuSelesai,
            'status'        => 'closed',
            'durasi'        => $this->calcDuration($log->waktu_mulai, $waktuSelesai),
        ]);

        return response()->json(['ok' => true, 'durasi' => $log->durasi, 'log' => $log->fresh()]);
    }

    /**
     * Buka ulang log
     * PATCH /admin/logs/{id}/reopen
     */
    public function reopen(ProblemLog $log)
    {
        $log->update([
            'waktu_selesai' => null,
            'status'        => 'open',
            'durasi'        => null,
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Edit waktu log
     * PATCH /admin/logs/{id}
     */
    public function update(Request $request, ProblemLog $log)
    {
        $request->validate([
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
        ]);

        $durasi = $request->filled('waktu_selesai')
            ? $this->calcDuration($request->waktu_mulai, $request->waktu_selesai)
            : null;

        $log->update([
            'waktu_mulai'   => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai ?: null,
            'status'        => $request->filled('waktu_selesai') ? 'closed' : 'open',
            'durasi'        => $durasi,
        ]);

        return response()->json(['ok' => true, 'log' => $log->fresh()]);
    }

    /**
     * Hapus log
     * DELETE /admin/logs/{id}
     */
    public function destroy(ProblemLog $log)
    {
        $log->delete();
        return response()->json(['ok' => true]);
    }

    /**
     * Ambil daftar log (AJAX)
     * GET /admin/logs/list
     */
    public function list(Request $request)
    {
        $logs = ProblemLog::where([
            'tanggal' => $request->tanggal,
            'factory' => $request->factory,
            'shift'   => $request->shift,
        ])->orderBy('waktu_mulai')->get();

        return response()->json($logs);
    }

    // ─── Helper ──────────────────────────────────────────────────────

    private function calcDuration(string $start, string $end): string
    {
        // Bersihkan nilai — format H:i atau H:i:s dari DB time column
        $start = substr($start, 0, 5);
        $end   = substr($end, 0, 5);

        $s    = strtotime("2000-01-01 {$start}");
        $e    = strtotime("2000-01-01 {$end}");
        $diff = $e - $s;
        if ($diff < 0) $diff += 86400; // melewati tengah malam

        $h = intdiv($diff, 3600);
        $m = intdiv($diff % 3600, 60);

        return $h > 0 ? "{$h}h {$m}m" : "{$m}m";
    }
}