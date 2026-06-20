<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sc;
use App\Models\Factory;
use App\Models\Section;
use App\Models\Status;
use App\Models\RepairDepartment;
use App\Models\SiteConfig;
use App\Models\AbsenceReason;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ScController extends Controller
{
    public function index()
    {
        $scs = Sc::withCount('factories')->orderBy('order_index')->get();
        // Also get admins for each SC
        foreach ($scs as $sc) {
            $sc->admin_user = User::where('sc_id', $sc->id)->where('role', 'admin')->first();
        }
        return view('admin.sc.index', compact('scs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:scs,slug',
            'short_label' => 'nullable|string|max:20',
            'admin_username' => 'required|string|max:50|unique:users,username',
            'admin_password' => 'required|string|min:6',
            'admin_name' => 'required|string|max:100',
        ]);

        return DB::transaction(function () use ($request) {
            $maxOrder = Sc::max('order_index') ?? 0;

            $sc = Sc::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'short_label' => $request->short_label,
                'gradient' => $request->gradient ?? 'linear-gradient(135deg,#1f3c88,#2e57d4)',
                'detail_departemen' => $request->detail_departemen,
                'order_index' => $maxOrder + 1,
            ]);

            // Clone Template from SC1
            $this->cloneTemplate(1, $sc->id);

            // Create Admin User for this SC
            User::create([
                'sc_id' => $sc->id,
                'name' => $request->admin_name,
                'username' => $request->admin_username,
                'password' => Hash::make($request->admin_password),
                'role' => 'admin',
                'factory' => [], 
            ]);

            return response()->json(['ok' => true, 'sc' => $sc], 201);
        });
    }

    public function update(Request $request, Sc $sc)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:scs,slug,' . $sc->id,
            'short_label' => 'nullable|string|max:20',
        ]);

        $sc->update($request->only(['name', 'slug', 'short_label', 'gradient', 'detail_departemen']));

        return response()->json(['ok' => true, 'sc' => $sc->fresh()]);
    }

    public function destroy(Sc $sc)
    {
        if ($sc->id === 1) {
            return response()->json(['ok' => false, 'message' => 'Cannot delete the default SC1.'], 403);
        }

        $sc->delete(); // Cascade will handle related data
        return response()->json(['ok' => true]);
    }

    private function cloneTemplate($fromScId, $toScId)
    {
        // NOTE: Factories & Sections are NOT cloned — each SC starts with blank groups.
        // The SC admin must create their own Groups via Group Management.

        // Clone Statuses
        $statuses = Status::where('sc_id', $fromScId)->get();
        foreach ($statuses as $s) {
            $newS = $s->replicate();
            $newS->sc_id = $toScId;
            $newS->save();
        }

        // Clone Repair Departments
        $depts = RepairDepartment::where('sc_id', $fromScId)->get();
        foreach ($depts as $d) {
            $newD = $d->replicate();
            $newD->sc_id = $toScId;
            $newD->save();
        }

        // Clone Site Configs
        $configs = SiteConfig::where('sc_id', $fromScId)->get();
        foreach ($configs as $c) {
            $newC = $c->replicate();
            $newC->sc_id = $toScId;
            $newC->save();
        }

        // Clone Absence Reasons
        $reasons = AbsenceReason::where('sc_id', $fromScId)->get();
        foreach ($reasons as $r) {
            $newR = $r->replicate();
            $newR->sc_id = $toScId;
            $newR->save();
        }
    }
}
