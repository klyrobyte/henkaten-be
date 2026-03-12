<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProblemLog;
use App\Services\FactoryConfigService;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function __construct(protected FactoryConfigService $factoryConfig) {}

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

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'        => 'required|date',
            'factory'        => 'required|string',
            'shift'          => 'required|in:A,B',
            'jenis'          => 'required|in:Machine,Material,Method',
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
            'created_by'     => auth()->id() ? (int) auth()->id() : null,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['ok' => true, 'log' => $log]);
        }

        return back()->with('success', "{$request->jenis} log ditambah: {$request->lokasi}");
    }

    public function close(ProblemLog $log)
    {
        $waktuSelesai = now('Asia/Jakarta')->format('H:i');
        $durasi       = $this->calcDuration($log->waktu_mulai, $waktuSelesai);

        $log->update([
            'waktu_selesai' => $waktuSelesai,
            'status'        => 'closed',
            'durasi'        => $durasi,
        ]);

        return response()->json(['ok' => true, 'durasi' => $durasi, 'waktu_selesai' => $waktuSelesai, 'log' => $log->fresh()]);
    }

    public function reopen(ProblemLog $log)
    {
        $log->update([
            'waktu_selesai' => null,
            'status'        => 'open',
            'durasi'        => null,
        ]);

        return response()->json(['ok' => true]);
    }

    public function update(Request $request, ProblemLog $log)
    {
        $request->validate([
            'waktu_mulai'   => 'required|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
        ]);

        $hasSelesai = $request->filled('waktu_selesai');
        $durasi     = $hasSelesai
            ? $this->calcDuration($request->waktu_mulai, $request->waktu_selesai)
            : null;

        $log->update([
            'waktu_mulai'   => $request->waktu_mulai,
            'waktu_selesai' => $hasSelesai ? $request->waktu_selesai : null,
            'status'        => $hasSelesai ? 'closed' : 'open',
            'durasi'        => $durasi,
        ]);

        return response()->json(['ok' => true, 'durasi' => $durasi, 'log' => $log->fresh()]);
    }

    public function destroy(ProblemLog $log)
    {
        $log->delete();
        return response()->json(['ok' => true]);
    }

    public function list(Request $request)
    {
        $logs = ProblemLog::where([
            'tanggal' => $request->tanggal,
            'factory' => $request->factory,
            'shift'   => $request->shift,
        ])->orderBy('waktu_mulai')->get();

        return response()->json($logs);
    }

    /**
     * Hitung durasi antara dua waktu HH:mm.
     * Handle lintas tengah malam (misal: 23:00 – 01:30 = 2h 30m).
     */
    private function calcDuration(string $start, string $end): string
    {
        $start = substr(trim($start), 0, 5);
        $end   = substr(trim($end),   0, 5);

        $s    = strtotime("2000-01-01 {$start}");
        $e    = strtotime("2000-01-01 {$end}");
        $diff = $e - $s;

        // Jika negatif = lintas tengah malam
        if ($diff < 0) $diff += 86400;

        $h = intdiv($diff, 3600);
        $m = intdiv($diff % 3600, 60);

        if ($h > 0 && $m > 0) return "{$h}j {$m}m";
        if ($h > 0)            return "{$h}j";
        return "{$m}m";
    }
}