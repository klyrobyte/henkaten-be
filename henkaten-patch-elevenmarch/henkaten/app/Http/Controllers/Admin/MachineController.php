<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\MachineStatus;
use App\Models\Member;
use App\Models\AssignmentReplacement;
use App\Services\FactoryConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MachineController extends Controller
{
    public function __construct(protected FactoryConfigService $factoryConfig)
    {
    }

    /**
     * Halaman daftar mesin  - redirect ke Dashboard.
     * Card mesin sudah dipindah ke halaman Dashboard.
     */
    public function index(Request $request)
    {
        return redirect()->route('admin.dashboard', array_filter([
            'tanggal' => $request->get('tanggal'),
        ]));
    }

    /**
     * Upload foto mesin
     * POST /admin/machines/photo
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'factory' => 'required|string',
            'machine_name' => 'required|string',
            'photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:3072',
        ]);

        $machine = Machine::firstOrCreate([
            'factory' => $request->factory,
            'name' => $request->machine_name,
        ]);

        // Delete old photo if exists
        if ($machine->photo && Storage::disk('public')->exists($machine->photo)) {
            Storage::disk('public')->delete($machine->photo);
        }

        // ── Downscale handler ────────────────────────────────────────────
        $file = $request->file('photo');
        $maxWidth = 1280;
        $maxHeight = 1280;
        $jpegQuality = 80;

        $image = imagecreatefromstring(file_get_contents($file->getRealPath()));

        $origW = imagesx($image);
        $origH = imagesy($image);

        if ($origW > $maxWidth || $origH > $maxHeight) {
            $ratio = min($maxWidth / $origW, $maxHeight / $origH);
            $newW = (int) round($origW * $ratio);
            $newH = (int) round($origH * $ratio);

            $resized = imagecreatetruecolor($newW, $newH);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            imagedestroy($image);
            $image = $resized;
        }

        $filename = 'machines/' . pathinfo($file->hashName(), PATHINFO_FILENAME) . '.jpg';

        ob_start();
        imagejpeg($image, null, $jpegQuality);
        $imageData = ob_get_clean();
        imagedestroy($image);

        Storage::disk('public')->put($filename, $imageData);
        // ── End downscale handler ────────────────────────────────────────

        $machine->update(['photo' => $filename]);

        return response()->json([
            'ok' => true,
            'photo_url' => Storage::url($filename),
        ]);
    }

    /**
     * Hapus foto mesin
     * DELETE /admin/machines/photo
     */
    public function deletePhoto(Request $request)
    {
        $request->validate([
            'factory' => 'required|string',
            'machine_name' => 'required|string',
        ]);

        $machine = Machine::where([
            'factory' => $request->factory,
            'name' => $request->machine_name,
        ])->first();

        if ($machine?->photo && Storage::disk('public')->exists($machine->photo)) {
            Storage::disk('public')->delete($machine->photo);
            $machine->update(['photo' => null]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Update status mesin
     * POST /admin/machines/status
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'factory' => 'required|string',
            'shift' => 'required|in:A,B',
            'machine_name' => 'required|string',
            'status' => 'required|in:normal,man,material,machine,method',
        ]);

        MachineStatus::updateOrCreate(
            [
                'tanggal' => $request->tanggal,
                'factory' => $request->factory,
                'shift' => $request->shift,
                'machine_name' => $request->machine_name,
            ],
            ['status' => $request->status]
        );

        return response()->json(['ok' => true, 'status' => $request->status]);
    }

    /**
     * Ambil semua status mesin
     */
    public function getStatuses(Request $request)
    {
        $statuses = MachineStatus::where([
            'tanggal' => $request->tanggal,
            'factory' => $request->factory,
            'shift' => $request->shift,
        ])->get()->keyBy('machine_name');

        return response()->json($statuses);
    }

    /**
     * 4M lights data  - otomatis berdasarkan:
     *   1. MachineStatus manual (override)
     *   2. ProblemLog open
     *   3. AbsenceRecord absen tanpa AssignmentReplacement → auto "man"
     */
    public function getLights(Request $request)
    {
        $tanggal = $request->tanggal;
        $factory = $request->factory;
        $shift = $request->shift;

        $statuses = \App\Models\MachineStatus::where(compact('tanggal', 'factory', 'shift'))
            ->where('status', '!=', 'normal')
            ->get();

        $logs = \App\Models\ProblemLog::where(compact('tanggal', 'factory', 'shift'))
            ->where('status', 'open')
            ->get();

        $absenIds = \App\Models\AbsenceRecord::where(compact('tanggal', 'factory', 'shift'))
            ->where('status', 'absen')
            ->pluck('member_id')
            ->toArray();

        $replacedIds = \App\Models\AssignmentReplacement::where(compact('tanggal', 'factory', 'shift'))
            ->pluck('member_id')
            ->toArray();

        $unreplacedIds = array_diff($absenIds, $replacedIds);

        $absenMachines = [];
        if (!empty($unreplacedIds)) {
            $absenMachines = \App\Models\Member::whereIn('id', $unreplacedIds)
                ->whereNotNull('mesin')
                ->pluck('mesin')
                ->toArray();
        }

        $lights = [];

        foreach ($statuses as $s) {
            $lights[$s->machine_name][] = $s->status;
        }
        foreach ($logs as $log) {
            $lights[$log->lokasi][] = strtolower($log->jenis);
        }
        foreach ($absenMachines as $mesin) {
            if (!in_array('man', $lights[$mesin] ?? [])) {
                $lights[$mesin][] = 'man';
            }
        }

        foreach ($lights as $k => $v) {
            $lights[$k] = array_values(array_unique($v));
        }

        return response()->json($lights);
    }
}