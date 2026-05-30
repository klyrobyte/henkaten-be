<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RepairDepartment;
use Illuminate\Http\Request;

/**
 * @group RepairDepartment
 * 
 * APIs for managing Repair Departments.
 */
class RepairDepartmentController extends Controller
{
    /**
     * GET /admin/repair-departments - management page
     */
    public function index()
    {
        $scId = auth()->user()->sc_id ?? 1;
        $departments = RepairDepartment::where('sc_id', $scId)->orderBy('id')->get();
        return view('admin.repair_departments.index', compact('departments'));
    }

    /**
     * POST /admin/repair-departments
     */
    public function store(Request $request)
    {
        $scId = auth()->user()->sc_id ?? 1;
        $request->validate([
            'name' => "required|string|max:100|unique:repair_departments,name,NULL,id,sc_id,{$scId}",
        ]);

        $department = RepairDepartment::create([
            'sc_id' => $scId,
            'name' => trim($request->name),
        ]);

        return response()->json(['ok' => true, 'department' => $department], 201);
    }

    /**
     * PUT /admin/repair-departments/{department}
     */
    public function update(Request $request, RepairDepartment $department)
    {
        $scId = auth()->user()->sc_id ?? 1;
        $request->validate([
            'name' => "required|string|max:100|unique:repair_departments,name,{$department->id},id,sc_id,{$scId}",
        ]);

        $department->update([
            'name' => trim($request->name),
        ]);

        return response()->json(['ok' => true, 'department' => $department->fresh()]);
    }

    /**
     * DELETE /admin/repair-departments/{department}
     */
    public function destroy(RepairDepartment $department)
    {
        $department->delete();
        return response()->json(['ok' => true]);
    }
}
