<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\Section;
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
    public function index()
    {
        $factories = Factory::orderBy('order_index')->get();
        return view('admin.group.index', compact('factories'));
    }

    /** GET /admin/api/factories  - list for dropdowns */
    public function apiList()
    {
        $factories = Factory::orderBy('order_index')->get();
        return response()->json(['ok' => true, 'factories' => $factories]);
    }

    /** POST /admin/api/factories */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:factories,name',
        ]);

        $name = trim($request->name);
        $slug = Factory::makeSlug($name);
        $short = Factory::makeShortLabel($name);
        $maxOrder = Factory::max('order_index') ?? 0;

        $factory = Factory::create([
            'name' => $name,
            'slug' => $slug,
            'short_label' => $request->short_label ?? $short,
            'gradient' => $request->gradient ?? 'linear-gradient(135deg,#546e7a,#78909c)',
            'warna_header' => $request->warna_header,
            'order_index' => $maxOrder + 1,
        ]);

        return response()->json(['ok' => true, 'factory' => $factory], 201);
    }

    /** PUT /admin/api/factories/{factory} */
    public function update(Request $request, Factory $factory)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:factories,name,' . $factory->id,
        ]);

        $name = trim($request->name);
        $factory->update([
            'name' => $name,
            'slug' => Factory::makeSlug($name),
            'short_label' => $request->short_label ?? Factory::makeShortLabel($name),
            'gradient' => $request->gradient ?? $factory->gradient,
            'warna_header' => $request->has('warna_header') ? $request->warna_header : $factory->warna_header,
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
