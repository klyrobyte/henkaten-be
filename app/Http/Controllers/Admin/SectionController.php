<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\Machine;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @group Section
 * 
 * APIs for managing Section.
 */
class SectionController extends Controller
{
    /** GET /admin/section  - management page */
    public function index()
    {
        $user = Auth::user();
        $query = Factory::orderBy('order_index')->with('sections');

        if (!$user->isSuperAdmin()) {
            $allowedFactories = (array) $user->factory;
            $query->whereIn('name', $allowedFactories);
        }

        $factories = $query->get();
        return view('admin.section.index', compact('factories'));
    }

    /** GET /admin/api/sections?factory_id=X  - list for dropdowns */
    public function apiList(Request $request)
    {
        $user = Auth::user();
        $query = Section::orderBy('order_index');

        if (!$user->isSuperAdmin()) {
            $allowedFactories = (array) $user->factory;
            $query->whereHas('factory', function ($q) use ($allowedFactories) {
                $q->whereIn('name', $allowedFactories);
            });
        }

        if ($request->filled('factory_id')) {
            $query->where('factory_id', $request->factory_id);
        }

        if ($request->filled('factory_name')) {
            if (!$user->isSuperAdmin() && !in_array($request->factory_name, (array) $user->factory)) {
                return response()->json(['ok' => true, 'sections' => []]);
            }
            $factory = Factory::where('name', $request->factory_name)->first();
            if ($factory) {
                $query->where('factory_id', $factory->id);
            }
        }

        $sections = $query->with('factory')->get();
        return response()->json(['ok' => true, 'sections' => $sections]);
    }

    /** POST /admin/api/sections */
    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'factory_id' => 'required|integer|exists:factories,id',
            'name' => 'required|string|max:150',
            'code' => 'required|string|max:50',
            'type' => 'nullable|string',
        ]);

        if (!$user->isSuperAdmin()) {
            $factory = Factory::find($request->factory_id);
            if (!$factory || !in_array($factory->name, (array) $user->factory)) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized factory scope.'], 403);
            }
        }

        // Auto-generate code from name if not meaningful
        $code = trim($request->code);

        // Check unique per factory
        $exists = Section::where('factory_id', $request->factory_id)
            ->where('code', $code)->exists();
        if ($exists) {
            return response()->json(['ok' => false, 'message' => 'Code sudah digunakan di factory ini.'], 422);
        }

        $maxOrder = Section::where('factory_id', $request->factory_id)->max('order_index') ?? 0;

        $section = Section::create([
            'factory_id' => $request->factory_id,
            'name' => $request->name,
            'code' => $code,
            'type' => $request->type,
            'is_key_persons' => ($request->type === 'persons'),
            'is_key_robot' => ($request->type === 'robot'),
            'order_index' => $maxOrder + 1,
        ]);

        return response()->json(['ok' => true, 'section' => $section->load('factory')], 201);
    }

    /** PUT /admin/api/sections/{section} */
    public function update(Request $request, Section $section)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            if (!$section->factory || !in_array($section->factory->name, (array) $user->factory)) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized factory scope.'], 403);
            }
        }

        $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'required|string|max:50',
            'type' => 'nullable|string',
        ]);

        $code = trim($request->code);

        // Check unique per factory (exclude self)
        $exists = Section::where('factory_id', $section->factory_id)
            ->where('code', $code)
            ->where('id', '!=', $section->id)
            ->exists();
        if ($exists) {
            return response()->json(['ok' => false, 'message' => 'Code sudah digunakan di factory ini.'], 422);
        }

        $section->update([
            'name' => $request->name,
            'code' => $code,
            'type' => $request->type,
            'is_key_persons' => ($request->type === 'persons'),
            'is_key_robot' => ($request->type === 'robot'),
        ]);

        return response()->json(['ok' => true, 'section' => $section->fresh()->load('factory')]);
    }

    /** DELETE /admin/api/sections/{section} */
    public function destroy(Request $request, Section $section)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            if (!$section->factory || !in_array($section->factory->name, (array) $user->factory)) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized factory scope.'], 403);
            }
        }

        // Check if any machines reference this section code
        $machineCount = Machine::where('section', $section->code)->count();
        if ($machineCount > 0 && !$request->boolean('force')) {
            return response()->json([
                'ok' => false,
                'confirm' => true,
                'message' => "Section \"{$section->name}\" masih digunakan oleh {$machineCount} mesin. Yakin ingin menghapus?",
            ], 409);
        }

        $section->delete();
        return response()->json(['ok' => true]);
    }

    /** PATCH /admin/api/sections/reorder  - bulk update order_index */
    public function reorder(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|integer|exists:sections,id',
            'order.*.order_index' => 'required|integer',
        ]);

        if (!$user->isSuperAdmin()) {
            $allowedFactories = (array) $user->factory;
            $sectionIds = collect($request->order)->pluck('id')->toArray();
            $unauthorizedCount = Section::whereIn('id', $sectionIds)
                ->whereHas('factory', function ($q) use ($allowedFactories) {
                    $q->whereNotIn('name', $allowedFactories);
                })->count();

            if ($unauthorizedCount > 0) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized factory scope for some sections.'], 403);
            }
        }

        DB::transaction(function () use ($request) {
            foreach ($request->order as $item) {
                Section::where('id', $item['id'])->update(['order_index' => $item['order_index']]);
            }
        });

        return response()->json(['ok' => true]);
    }
}
