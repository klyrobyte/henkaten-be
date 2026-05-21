<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\Status;
use Illuminate\Http\Request;

/**
 * @group Status
 * 
 * APIs for managing Status.
 */
class StatusController extends Controller
{
    /** GET /admin/status  - management page */
    public function index()
    {
        $statuses = Status::orderBy('order_index')->get();
        return view('admin.status.index', compact('statuses'));
    }

    /** GET /admin/api/statuses */
    public function apiList()
    {
        $statuses = Status::orderBy('order_index')->get();
        return response()->json(['ok' => true, 'statuses' => $statuses]);
    }

    /** POST /admin/api/statuses */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:50|unique:statuses,key',
            'label' => 'required|string|max:100',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:30',
        ]);

        $maxOrder = Status::max('order_index') ?? 0;

        $status = Status::create([
            'key' => strtolower(trim($request->key)),
            'label' => $request->label,
            'icon' => $request->icon ?? '⚙️',
            'color' => $request->color ?? '#546e7a',
            'order_index' => $maxOrder + 1,
        ]);

        return response()->json(['ok' => true, 'status' => $status], 201);
    }

    /** PUT /admin/api/statuses/{status} */
    public function update(Request $request, Status $status)
    {
        $request->validate([
            'key' => 'required|string|max:50|unique:statuses,key,' . $status->id,
            'label' => 'required|string|max:100',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:30',
        ]);

        $status->update([
            'key' => strtolower(trim($request->key)),
            'label' => $request->label,
            'icon' => $request->icon ?? $status->icon,
            'color' => $request->color ?? $status->color,
        ]);

        return response()->json(['ok' => true, 'status' => $status->fresh()]);
    }

    /** DELETE /admin/api/statuses/{status} */
    public function destroy(Request $request, Status $status)
    {
        $machineCount = Machine::where('status', $status->key)->count();
        if ($machineCount > 0 && !$request->boolean('force')) {
            return response()->json([
                'ok' => false,
                'confirm' => true,
                'message' => "Status \"{$status->label}\" masih digunakan oleh {$machineCount} mesin. Yakin ingin menghapus?",
            ], 409);
        }

        $status->delete();
        return response()->json(['ok' => true]);
    }
}
