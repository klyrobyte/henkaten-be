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
        $departments = RepairDepartment::orderBy('id')->get();
        return view('admin.repair_departments.index', compact('departments'));
    }

    /**
     * POST /admin/repair-departments
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:repair_departments,name',
        ]);

        $department = RepairDepartment::create([
            'name' => trim($request->name),
        ]);

        return response()->json(['ok' => true, 'department' => $department], 201);
    }

    /**
     * PUT /admin/repair-departments/{department}
     */
    public function update(Request $request, RepairDepartment $department)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:repair_departments,name,' . $department->id,
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
