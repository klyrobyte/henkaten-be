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
use Illuminate\Support\Facades\Auth;

/**
 * @group Machine
 * 
 * APIs for managing Machine.
 */
class MachineController extends Controller
{
    public function __construct(protected FactoryConfigService $factoryConfig)
    {
    }

    /**
     * Check if the user has access to the given factory.
     */
    private function isFactoryInScope(?string $factory): bool
    {
        if (!$factory)
            return false;
        $user = Auth::user();
        if (!$user)
            return false;
        if ($user->isSuperAdmin()) {
            return true;
        }

        $allowedFactories = (array) $user->factory;

        // Exact match
        if (in_array($factory, $allowedFactories)) {
            return true;
        }

        // Case-insensitive match or strtoupper match (common for short labels like F2)
        $target = strtoupper($factory);
        foreach ($allowedFactories as $f) {
            if (strtoupper($f) === $target) {
                return true;
            }
        }

        return false;
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

        if (!$this->isFactoryInScope($request->factory)) {
            return response()->json(['error' => 'Unauthorized factory access'], 403);
        }

        $scId = auth()->user()->sc_id ?? 1;
        $machine = Machine::firstOrCreate([
            'sc_id' => $scId,
            'factory' => $request->factory,
            'name' => $request->machine_name,
        ]);

        // Delete old photo if exists
        if ($machine->photo) {
            $delPath = str_starts_with($machine->photo, 'machines/')
                ? basename($machine->photo)
                : $machine->photo;
            if (Storage::disk('machines')->exists($delPath)) {
                Storage::disk('machines')->delete($delPath);
            }
        }

        // ── Downscale handler ────────────────────────────────────────────
        $file = $request->file('photo');
        $maxWidth = 1280;   // cap longest side to 1280 px
        $maxHeight = 1280;
        $jpegQuality = 80;     // 80 % quality  - good balance size vs clarity

        $image = imagecreatefromstring(file_get_contents($file->getRealPath()));

        $origW = imagesx($image);
        $origH = imagesy($image);

        // Only resize if the image actually exceeds the cap
        if ($origW > $maxWidth || $origH > $maxHeight) {
            $ratio = min($maxWidth / $origW, $maxHeight / $origH);
            $newW = (int) round($origW * $ratio);
            $newH = (int) round($origH * $ratio);

            $resized = imagecreatetruecolor($newW, $newH);

            // Preserve transparency for PNG
            imagealphablending($resized, false);
            imagesavealpha($resized, true);

            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            imagedestroy($image);
            $image = $resized;
        }

        // Always save as JPEG to keep file size small
        $filename = pathinfo($file->hashName(), PATHINFO_FILENAME) . '.jpg';
        $diskPath = Storage::disk('machines')->path('');   // absolute path to disk root

        ob_start();
        imagejpeg($image, null, $jpegQuality);
        $imageData = ob_get_clean();
        imagedestroy($image);

        Storage::disk('machines')->put($filename, $imageData);
        // ── End downscale handler ────────────────────────────────────────

        $machine->update(['photo' => $filename]);

        return response()->json([
            'ok' => true,
            'photo_url' => '/storage/machines/' . $filename,
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

        if (!$this->isFactoryInScope($request->factory)) {
            return response()->json(['error' => 'Unauthorized factory access'], 403);
        }

        $scId = auth()->user()->sc_id ?? 1;
        $machine = Machine::where([
            'sc_id' => $scId,
            'factory' => $request->factory,
            'name' => $request->machine_name,
        ])->first();

        if ($machine?->photo) {
            $delPath = str_starts_with($machine->photo, 'machines/')
                ? basename($machine->photo)
                : $machine->photo;
            if (Storage::disk('machines')->exists($delPath)) {
                Storage::disk('machines')->delete($delPath);
            }
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

        if (!$this->isFactoryInScope($request->factory)) {
            return response()->json(['error' => 'Unauthorized factory access'], 403);
        }

        $scId = auth()->user()->sc_id ?? 1;
        MachineStatus::updateOrCreate(
            [
                'sc_id' => $scId,
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
        if (!$this->isFactoryInScope($request->factory)) {
            return response()->json(['error' => 'Unauthorized factory access'], 403);
        }

        $scId = auth()->user()->sc_id ?? 1;
        $statuses = MachineStatus::where([
            'sc_id' => $scId,
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

        if (!$this->isFactoryInScope($factory)) {
            return response()->json(['error' => 'Unauthorized factory access'], 403);
        }

        $scId = auth()->user()->sc_id ?? 1;

        $statuses = \App\Models\MachineStatus::where(compact('sc_id', 'tanggal', 'factory', 'shift'))
            ->where('status', '!=', 'normal')
            ->get();

        $logs = \App\Models\ProblemLog::where(compact('sc_id', 'tanggal', 'factory', 'shift'))
            ->where('status', 'open')
            ->get();

        $absenIds = \App\Models\AbsenceRecord::where(compact('sc_id', 'tanggal', 'factory', 'shift'))
            ->where('status', 'absen')
            ->pluck('member_id')
            ->toArray();

        $replacedIds = \App\Models\AssignmentReplacement::where(compact('sc_id', 'tanggal', 'factory', 'shift'))
            ->pluck('member_id')
            ->toArray();

        $unreplacedIds = array_diff($absenIds, $replacedIds);

        $absenMachines = [];
        if (!empty($unreplacedIds)) {
            $absenMachines = \App\Models\Member::where('sc_id', $scId)
                ->whereIn('id', $unreplacedIds)
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

    /**
     * Update floor plan coordinates for a machine
     * PATCH /admin/machines/{id}/floor-coordinates
     */
    public function updateFloorCoordinates(Request $request, Machine $machine)
    {
        $request->validate([
            'floor_cx' => 'nullable|numeric',
            'floor_cy' => 'nullable|numeric',
            'floor_plan' => 'nullable|string',
        ]);

        if (!$this->isFactoryInScope($machine->factory)) {
            return response()->json(['error' => 'Unauthorized factory access'], 403);
        }

        $scId = auth()->user()->sc_id ?? 1;
        if ($machine->sc_id != $scId) {
             return response()->json(['error' => 'Unauthorized SC access'], 403);
        }

        $machine->update([
            'floor_cx' => $request->floor_cx,
            'floor_cy' => $request->floor_cy,
            'floor_plan' => $request->floor_plan,
        ]);

        return response()->json([
            'success' => true,
            'message' => $request->floor_cx && $request->floor_cy ? 'Coordinates updated' : 'Coordinates deleted',
            'machine_name' => $machine->name,
            'coordinates' => [
                'cx' => $machine->floor_cx,
                'cy' => $machine->floor_cy,
            ]
        ]);
    }

    /**
     * Show floor plan editor
     * GET /admin/machines/floor-plan-editor
     */
    public function showFloorPlanEditor(Request $request)
    {
        $factory = $request->get('factory', 'f2');

        if (!$this->isFactoryInScope($factory)) {
            abort(403);
        }

        $scId = auth()->user()->sc_id ?? 1;
        $machines = Machine::where('sc_id', $scId)
            ->where('factory', strtoupper($factory))
            ->orderBy('name')
            ->get();

        return view('admin.machines.floor-plan-editor', [
            'factory' => $factory,
            'machines' => $machines,
        ]);
    }

    /**
     * Show floor plan display
     * GET /machines/floor-plan
     */
    public function showFloorPlan(Request $request)
    {
        $factory = $request->get('factory', 'F2');

        if (!$this->isFactoryInScope($factory)) {
            abort(403);
        }

        return view('machines.floor-plan', [
            'factory' => $factory,
        ]);
    }
}
