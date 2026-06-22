<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\Sc;
use App\Models\Section;
use App\Services\ScContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group Factory
 * 
 * APIs for managing Factory.
 */
class FactoryController extends Controller
{
    /** GET /admin/group  - management page */
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            // Super Admin: show SC dropdown and filter by chosen SC
            $scs = Sc::orderBy('order_index')->get();
            // Default to the SC ID from query param, or fall back to SC1
            $activeSc = (int) ($request->query('sc_id', 1));
            $factories = Factory::where('sc_id', $activeSc)->orderBy('order_index')->get();
        } else {
            // Regular admin: scoped to their own SC only
            $scs = collect();
            $activeSc = ScContext::id();
            $factories = Factory::where('sc_id', $activeSc)->orderBy('order_index')->get();
        }

        return view('admin.group.index', compact('factories', 'scs', 'activeSc'));
    }

    /** GET /admin/api/factories  - list for dropdowns */
    public function apiList()
    {
        $scId = ScContext::id();
        $factories = Factory::where('sc_id', $scId)->orderBy('order_index')->get();
        return response()->json(['ok' => true, 'factories' => $factories]);
    }

    /** POST /admin/api/factories */
    public function store(Request $request)
    {
        $user = auth()->user();

        // Super Admin can supply sc_id to create a factory for another SC.
        // Regular admins are always scoped to their own SC.
        if ($user->isSuperAdmin() && $request->filled('sc_id')) {
            $scId = (int) $request->sc_id;
        } else {
            $scId = ScContext::id();
        }

        $request->validate([
            'name' => "required|string|max:100|unique:factories,name,NULL,id,sc_id,{$scId}",
            'detail_departemen' => 'nullable|string|max:255',
        ]);

        $name = trim($request->name);
        $slug = Factory::makeSlug($name);
        $short = Factory::makeShortLabel($name);
        $maxOrder = Factory::where('sc_id', $scId)->max('order_index') ?? 0;

        $factory = Factory::create([
            'sc_id' => $scId,
            'name' => $name,
            'slug' => $slug,
            'short_label' => $request->short_label ?? $short,
            'gradient' => $request->gradient ?? 'linear-gradient(135deg,#546e7a,#78909c)',
            'order_index' => $maxOrder + 1,
            'detail_departemen' => $request->detail_departemen,
        ]);

        return response()->json(['ok' => true, 'factory' => $factory], 201);
    }

    /** PUT /admin/api/factories/{factory} */
    public function update(Request $request, Factory $factory)
    {
        $scId = $factory->sc_id;
        $request->validate([
            'name' => "required|string|max:100|unique:factories,name,{$factory->id},id,sc_id,{$scId}",
            'detail_departemen' => 'nullable|string|max:255',
        ]);

        $name = trim($request->name);
        $factory->update([
            'name' => $name,
            'slug' => Factory::makeSlug($name),
            'short_label' => $request->short_label ?? Factory::makeShortLabel($name),
            'gradient' => $request->gradient ?? $factory->gradient,
            'detail_departemen' => $request->detail_departemen,
        ]);

        return response()->json(['ok' => true, 'factory' => $factory->fresh()]);
    }

    /** DELETE /admin/api/factories/{factory} */
    public function destroy(Request $request, Factory $factory)
    {
        $sectionCount = $factory->sections()->count();
        if ($sectionCount > 0 && !$request->boolean('force')) {
            return response()->json([
                'ok' => false,
                'confirm' => true,
                'message' => "Factory \"{$factory->name}\" masih memiliki {$sectionCount} section. Yakin ingin menghapus?",
            ], 409);
        }

        // If forced, cascade delete sections (machines.factory stays as string  - no cascade needed)
        $factory->sections()->delete();
        $factory->delete();

        return response()->json(['ok' => true]);
    }

    /** PATCH /admin/api/factories/reorder  - bulk update order_index */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|integer|exists:factories,id',
            'order.*.order_index' => 'required|integer',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->order as $item) {
                Factory::where('id', $item['id'])->update(['order_index' => $item['order_index']]);
            }
        });

        return response()->json(['ok' => true]);
    }
}
