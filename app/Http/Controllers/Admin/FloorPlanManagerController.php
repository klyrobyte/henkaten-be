<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\FactoryLayout;
use App\Models\Machine;
use App\Services\ScContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * FloorPlanManagerController — Admin page untuk mengelola layout pabrik & pin mesin
 *
 * Fitur:
 * 1. Upload foto layout pabrik per factory
 * 2. Tampilkan gambar layout dengan overlay pin mesin (interaktif)
 * 3. Simpan koordinat X,Y mesin (klik pada gambar → simpan ke machines.floor_cx/cy)
 * 4. Tabel daftar mesin per area dengan koordinat yang sudah disimpan
 */
class FloorPlanManagerController extends Controller
{
    // GET /admin/floor-plan-manager
    public function index(Request $request)
    {
        $user = Auth::user();
        $scId = ScContext::id();
        $isSA = $user->isSuperAdmin();
        $allowed = (array) $user->factory;

        $factoriesQ = Factory::where('sc_id', $scId)->orderBy('order_index');
        if (!$isSA) {
            $factoriesQ->whereIn('name', $allowed);
        }
        $factories = $factoriesQ->get();

        $factory = $request->get('factory', $factories->first()?->name ?? '');
        if (!$isSA && !in_array($factory, $allowed)) {
            $factory = $allowed[0] ?? '';
        }

        // Layout foto untuk factory ini
        $layout = FactoryLayout::where('sc_id', $scId)
            ->where('factory', $factory)
            ->first();

        // Daftar mesin di factory ini beserta koordinat
        $machines = Machine::where('sc_id', $scId)
            ->where('factory', $factory)
            ->orderBy('name')
            ->get(['id', 'name', 'status', 'section', 'floor_cx', 'floor_cy']);

        return view('admin.floor_plan_manager', compact(
            'factories',
            'factory',
            'layout',
            'machines'
        ));
    }

    // POST /admin/floor-plan-manager/upload  — upload gambar layout
    public function uploadLayout(Request $request)
    {
        $request->validate([
            'factory' => 'required|string',
            'layout' => 'required|image|max:5120', // max 5MB
        ]);

        $user = Auth::user();
        $scId = ScContext::id();
        $factory = $request->factory;

        if (!$user->isSuperAdmin() && !in_array($factory, (array) $user->factory)) {
            abort(403);
        }

        $file = $request->file('layout');
        $img = imagecreatefromstring(file_get_contents($file->getRealPath()));
        $width = $img ? imagesx($img) : null;
        $height = $img ? imagesy($img) : null;
        if ($img)
            imagedestroy($img);

        // Simpan file
        $filename = 'layout_' . $scId . '_' . \Str::slug($factory) . '_' . time() . '.' . $file->getClientOriginalExtension();
        // @rizkydaffy: write directly to public/storage/layouts/ so the URL resolves without storage:link
        $destDir = public_path('storage/layouts');
        if (!is_dir($destDir)) {
            mkdir($destDir, 0775, true);
        }
        $file->move($destDir, $filename);
        // Keep Storage record in sync
        Storage::disk('public')->put('layouts/' . $filename, file_get_contents($destDir . '/' . $filename));

        $layout = FactoryLayout::updateOrCreate(
            ['sc_id' => $scId, 'factory' => $factory],
            [
                'layout_image' => $filename,
                'layout_width' => $width,
                'layout_height' => $height,
            ]
        );

        return response()->json([
            'ok' => true,
            'url' => $layout->image_url,
            'width' => $layout->layout_width,
            'height' => $layout->layout_height,
        ]);
    }

    // PATCH /admin/floor-plan-manager/pin/{machine}  — simpan koordinat pin mesin
    public function updatePin(Request $request, Machine $machine)
    {
        $request->validate([
            'floor_cx' => 'nullable|numeric',
            'floor_cy' => 'nullable|numeric',
            'floor_plan' => 'nullable|string|max:50',
        ]);

        $user = Auth::user();
        $scId = ScContext::id();

        if ((int) $machine->sc_id !== $scId) {
            abort(403, 'Unauthorized SC access.');
        }

        if (!$user->isSuperAdmin() && !in_array($machine->factory, (array) $user->factory)) {
            abort(403, 'Unauthorized factory access.');
        }

        $machine->update([
            'floor_cx' => $request->floor_cx,
            'floor_cy' => $request->floor_cy,
            'floor_plan' => $request->floor_plan,
        ]);

        return response()->json([
            'ok' => true,
            'machine_id' => $machine->id,
            'machine_name' => $machine->name,
            'floor_cx' => $machine->floor_cx,
            'floor_cy' => $machine->floor_cy,
        ]);
    }

    // GET /admin/floor-plan-manager/machines  — JSON daftar mesin + koordinat
    public function machineList(Request $request)
    {
        $scId = ScContext::id();
        $factory = $request->get('factory', ScContext::firstFactory());

        $machines = Machine::where('sc_id', $scId)
            ->where('factory', $factory)
            ->orderBy('name')
            ->get(['id', 'name', 'status', 'section', 'floor_cx', 'floor_cy', 'floor_plan']);

        return response()->json($machines);
    }
}
