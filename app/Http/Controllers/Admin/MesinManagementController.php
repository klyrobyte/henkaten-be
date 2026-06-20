<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\Machine;
use App\Models\Section;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @group Mesin Management
 * 
 * APIs for managing Mesin Management.
 */
class MesinManagementController extends Controller
{

    /**
     * Tentukan factory aktif berdasarkan role user:
     * - admin → dari session (bisa ganti lewat context switcher)
     * - gl    → dari kolom factory user (tidak bisa ganti)
     * develop by rizky
     */
    private function getCurrentFactory(Request $request): string
    {
        $user = Auth::user();
        $scId = $user->sc_id ?? 1;

        // 1. Superadmin: session-based full access
        if ($user->isSuperAdmin()) {
            return $request->session()->get('factory', Factory::where('sc_id', $scId)->orderBy('order_index')->value('name') ?? 'Factory 2');
        }

        // 2. GL: fixed to their assigned factory
        if ($user->role === 'gl') {
            return (is_array($user->factory) ? ($user->factory[0] ?? null) : $user->factory) ?? 'Factory 2';
        }

        // 3. Normal Admin (role='admin'): restricted to (array)$user->factory
        $allowedFactories = (array) ($user->factory ?? []);
        $sessionFactory = $request->session()->get('factory', 'Factory 2');

        // If session factory is allowed, use it; otherwise fallback to first allowed
        if (in_array($sessionFactory, $allowedFactories)) {
            return $sessionFactory;
        }

        return !empty($allowedFactories) ? $allowedFactories[0] : (Factory::where('sc_id', $scId)->orderBy('order_index')->value('name') ?? 'Factory 2');
    }

    /**
     * Halaman daftar & kelola mesin (Mesin Management)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $scId = $user->sc_id ?? 1;
        $currentFactory = $this->getCurrentFactory($request);

        $machines = Machine::where('sc_id', $scId)->where('factory', $currentFactory)
            ->orderBy('name')
            ->get();

        $stats = [
            'total' => $machines->count(),
            'mesin' => $machines->where('status', 'mesin')->count(),
            'robot' => $machines->where('status', 'robot')->count(),
            'persons' => $machines->where('status', 'persons')->count(),
            'line' => $machines->where('status', 'line')->count(),
            'pos' => $machines->where('status', 'pos')->count(),
            'lainya' => $machines->where('status', 'lainya')->count(),
            'mc_vibration' => $machines->where('status', 'mc_vibration')->count(),
        ];

        $userRole = $user->role;
        $userFactory = $user->factory;

        // Dynamic data for form dropdowns
        $factoriesQuery = Factory::where('sc_id', $scId);
        $sectionsQuery = Section::whereHas('factory', function($q) use ($scId) {
            $q->where('sc_id', $scId);
        })->with('factory');

        if (!$user->isSuperAdmin()) {
            $allowed = (array) ($user->factory ?? []);
            $factoriesQuery->whereIn('name', $allowed);
            $sectionsQuery->whereHas('factory', function ($q) use ($allowed) {
                $q->whereIn('name', $allowed);
            });
        }

        $factories = $factoriesQuery->orderBy('order_index')->get();
        $sections = $sectionsQuery->orderBy('factory_id')->orderBy('order_index')->get();
        $statuses = Status::where('sc_id', $scId)->orderBy('order_index')->get();

        return view(
            'admin.mesinmg.index',
            compact('machines', 'stats', 'currentFactory', 'userRole', 'userFactory', 'factories', 'sections', 'statuses')
        );
    }

    /**
     * GET /admin/mesinmg/{id}  - detail mesin (JSON)
     */
    public function show(Machine $machine)
    {
        $user = Auth::user();
        $scId = $user->sc_id ?? 1;

        if ($machine->sc_id != $scId) {
             abort(403, 'Unauthorized access to this machine.');
        }

        if (!$user->isSuperAdmin() && !in_array($machine->factory, (array) ($user->factory ?? []))) {
            abort(403, 'Unauthorized factory access.');
        }

        return response()->json([
            'ok' => true,
            'machine' => array_merge($machine->toArray(), [
                'photo_url' => $machine->photo_url,
            ]),
        ]);
    }

    /**
     * POST /admin/mesinmg  - tambah mesin baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $scId = $user->sc_id ?? 1;
        $currentFactory = $this->getCurrentFactory($request);

        // Build dynamic validation rules from DB
        $factoryNames = Factory::where('sc_id', $scId)->pluck('name')->toArray();
        $sectionCodes = Section::whereHas('factory', function($q) use ($scId) {
            $q->where('sc_id', $scId);
        })->pluck('code')->toArray();
        $statusKeys = Status::where('sc_id', $scId)->pluck('key')->toArray();

        $factoryRule = count($factoryNames) ? 'nullable|string|in:' . implode(',', $factoryNames) : 'nullable|string';
        $sectionRule = count($sectionCodes) ? 'nullable|string|in:' . implode(',', $sectionCodes) : 'nullable|string';
        $statusRule = count($statusKeys) ? 'required|string|in:' . implode(',', $statusKeys) : 'required|string';

        $request->validate([
            'name' => 'required|string|max:100',
            'status' => $statusRule,
            'photo_base64' => 'nullable|string',
            'factory' => $factoryRule,
            'section' => $sectionRule,
        ]);

        $factory = ($user->role === 'gl')
            ? $currentFactory
            : ($request->factory ?? $currentFactory);

        // Scope validation
        if (!$user->isSuperAdmin() && !in_array($factory, (array) ($user->factory ?? []))) {
            abort(403, 'Unauthorized factory access.');
        }

        $section = $request->section ?: null;

        $photoPath = null;
        if ($request->filled('photo_base64')) {
            $photoPath = $this->saveBase64Photo($request->photo_base64);
        }

        $machine = Machine::create([
            'sc_id' => $scId,
            'factory' => $factory,
            'name' => $request->name,
            'status' => $request->status,
            'section' => $section,
            'photo' => $photoPath,
        ]);

        return response()->json([
            'ok' => true,
            'machine' => array_merge($machine->toArray(), [
                'photo_url' => $machine->photo_url,
            ]),
        ]);
    }

    /**
     * PUT /admin/mesinmg/{id}  - update mesin
     */
    public function update(Request $request, Machine $machine)
    {
        $user = Auth::user();
        $scId = $user->sc_id ?? 1;

        if ($machine->sc_id != $scId) {
             abort(403, 'Unauthorized access to this machine.');
        }

        // 1. Validate current machine's factory
        if (!$user->isSuperAdmin() && !in_array($machine->factory, (array) ($user->factory ?? []))) {
            abort(403, 'Unauthorized factory access.');
        }

        $currentFactory = $this->getCurrentFactory($request);

        // Build dynamic validation rules from DB
        $factoryNames = Factory::where('sc_id', $scId)->pluck('name')->toArray();
        $sectionCodes = Section::whereHas('factory', function($q) use ($scId) {
            $q->where('sc_id', $scId);
        })->pluck('code')->toArray();
        $statusKeys = Status::where('sc_id', $scId)->pluck('key')->toArray();

        $factoryRule = count($factoryNames) ? 'nullable|string|in:' . implode(',', $factoryNames) : 'nullable|string';
        $sectionRule = count($sectionCodes) ? 'nullable|string|in:' . implode(',', $sectionCodes) : 'nullable|string';
        $statusRule = count($statusKeys) ? 'required|string|in:' . implode(',', $statusKeys) : 'required|string';

        $request->validate([
            'name' => 'required|string|max:100',
            'status' => $statusRule,
            'photo_base64' => 'nullable|string',
            'factory' => $factoryRule,
            'section' => $sectionRule,
        ]);

        $factory = ($user->role === 'gl')
            ? $currentFactory
            : ($request->factory ?? $machine->factory ?? $currentFactory);

        // 2. Validate target factory
        if (!$user->isSuperAdmin() && !in_array($factory, (array) ($user->factory ?? []))) {
            abort(403, 'Unauthorized factory access.');
        }

        $data = [
            'name' => $request->name,
            'status' => $request->status,
            'factory' => $factory,
            'section' => $request->section ?: null,
        ];

        if ($request->filled('photo_base64') && $request->photo_base64 !== 'remove') {
            if ($machine->photo) {
                $delPath = str_starts_with($machine->photo, 'machines/')
                    ? basename($machine->photo) : $machine->photo;
                if (Storage::disk('machines')->exists($delPath)) {
                    Storage::disk('machines')->delete($delPath);
                }
            }
            $data['photo'] = $this->saveBase64Photo($request->photo_base64);
        }

        if ($request->photo_base64 === 'remove') {
            if ($machine->photo) {
                $delPath = str_starts_with($machine->photo, 'machines/')
                    ? basename($machine->photo) : $machine->photo;
                if (Storage::disk('machines')->exists($delPath)) {
                    Storage::disk('machines')->delete($delPath);
                }
            }
            $data['photo'] = null;
        }

        $machine->update($data);

        return response()->json([
            'ok' => true,
            'machine' => array_merge($machine->fresh()->toArray(), [
                'photo_url' => $machine->fresh()->photo_url,
            ]),
        ]);
    }

    /**
     * DELETE /admin/mesinmg/{id}  - hapus mesin
     */
    public function destroy(Machine $machine)
    {
        $user = Auth::user();
        $scId = $user->sc_id ?? 1;

        if ($machine->sc_id != $scId) {
             abort(403, 'Unauthorized access to this machine.');
        }

        if (!$user->isSuperAdmin() && !in_array($machine->factory, (array) ($user->factory ?? []))) {
            abort(403, 'Unauthorized factory access.');
        }

        if ($machine->photo) {
            $delPath = str_starts_with($machine->photo, 'machines/')
                ? basename($machine->photo) : $machine->photo;
            if (Storage::disk('machines')->exists($delPath)) {
                Storage::disk('machines')->delete($delPath);
            }
        }
        $machine->delete();

        return response()->json(['ok' => true]);
    }

    /**
     * Helper: simpan foto base64 ke disk machines
     */
    private function saveBase64Photo(string $base64): string
    {
        // Hapus data URI prefix jika ada
        if (str_contains($base64, ',')) {
            [, $base64] = explode(',', $base64, 2);
        }
        $decoded = base64_decode($base64);
        $filename = Str::uuid() . '.jpg';
        Storage::disk('machines')->put($filename, $decoded);
        return $filename;
    }
}
