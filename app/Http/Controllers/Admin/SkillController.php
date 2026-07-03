<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\Member;
use App\Models\MemberSkill;
use App\Models\Machine;
use App\Models\MachineProcess;
use App\Services\ScContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * SkillController — Manajemen Skill Member per Mesin
 *
 * Halaman ini memungkinkan admin/GL untuk mengatur persentase
 * kemampuan (skill) setiap member pada setiap mesin/pos yang
 * ada di factory mereka.
 *
 * Skill % ≥ 75 = berhak menjadi pengganti untuk mesin tersebut.
 */
class SkillController extends Controller
{
    // GET /admin/skills  — halaman utama
    public function index(Request $request)
    {
        $user    = Auth::user();
        $scId    = ScContext::id();
        $isSA    = $user->isSuperAdmin();
        $allowed = (array) $user->factory;

        // Factories yang bisa diakses
        $factoriesQ = Factory::where('sc_id', $scId)->orderBy('order_index');
        if (!$isSA) {
            $factoriesQ->whereIn('name', $allowed);
        }
        $factories = $factoriesQ->get();

        $factory = $request->get('factory', $factories->first()?->name ?? '');
        if (!$isSA && !in_array($factory, $allowed)) {
            $factory = $allowed[0] ?? '';
        }

        $shift = $request->get('shift', 'all');

        // Daftar mesin di factory ini
        $machines = Machine::where('sc_id', $scId)
            ->where('factory', $factory)
            ->orderBy('name')
            ->pluck('name')
            ->unique()
            ->values();

        // Daftar member
        $memberQ = Member::where('sc_id', $scId)
            ->where('factory', $factory)
            ->where('status', 'active')
            ->orderBy('nama');

        if ($shift !== 'all') {
            $memberQ->where('shift', $shift);
        }

        $members = $memberQ->get();

        // Load all defined processes for the factory
        $machineProcesses = MachineProcess::where('sc_id', $scId)
            ->where('factory', $factory)
            ->orderBy('id')
            ->get()
            ->groupBy('machine_name');

        // Load semua skill untuk factory ini (keyed by member_id + machine_name + process_name)
        // Since we are changing structure, let's pass the flat collection and filter in the view or restructure here.
        // Let's restructure: skills[member_id][machine_name][process_name] = pct
        $rawSkills = MemberSkill::where('sc_id', $scId)
            ->where('factory', $factory)
            ->get();
            
        $skills = [];
        foreach ($rawSkills as $s) {
            $pName = $s->process_name ?: '-'; // Fallback if no process
            $skills[$s->member_id][$s->machine_name][$pName] = $s;
        }

        return view('admin.skill.index', compact(
            'factories', 'factory', 'shift', 'machines', 'members', 'skills', 'machineProcesses'
        ));
    }

    // POST /api/skills/save  — simpan satu skill (AJAX)
    public function save(Request $request)
    {
        $request->validate([
            'member_id'    => 'required|integer|exists:members,id',
            'machine_name' => 'required|string|max:100',
            'process_name' => 'nullable|string|max:150',
            'factory'      => 'required|string',
            'skill_pct'    => 'required|integer|min:0|max:100',
        ]);

        $user  = Auth::user();
        $scId  = ScContext::id();

        // Guard: member harus milik SC ini
        $member = Member::where('id', $request->member_id)
            ->where('sc_id', $scId)
            ->firstOrFail();

        if (!$user->isSuperAdmin() && !in_array($member->factory, (array) $user->factory)) {
            abort(403, 'Unauthorized factory access.');
        }

        $skill = MemberSkill::updateOrCreate(
            [
                'sc_id'        => $scId,
                'member_id'    => $member->id,
                'machine_name' => $request->machine_name,
                'process_name' => $request->process_name ?: null,
                'factory'      => $request->factory,
            ],
            [
                'skill_pct'  => $request->skill_pct,
                'updated_by' => $user->id,
            ]
        );

        return response()->json([
            'ok'        => true,
            'skill_pct' => $skill->skill_pct,
            'eligible'  => $skill->isEligibleForReplacement(),
        ]);
    }

    public function saveBatch(Request $request)
    {
        try {
            $request->validate([
                'skills' => 'required|array',
                'skills.*.member_id' => 'required|integer',
                'skills.*.machine_name' => 'required|string',
                'skills.*.process_name' => 'nullable|string',
                'skills.*.factory' => 'required|string',
                'skills.*.skill_pct' => 'required|integer|min:0|max:100',
            ]);

            $user = Auth::user();
            $scId = ScContext::id();

            foreach ($request->skills as $s) {
                $member = Member::where('id', $s['member_id'])->where('sc_id', $scId)->first();
                if (!$member || (!$user->isSuperAdmin() && !in_array($member->factory, (array) $user->factory))) {
                    continue;
                }

                MemberSkill::updateOrCreate(
                    [
                        'sc_id' => $scId,
                        'member_id' => $member->id,
                        'machine_name' => $s['machine_name'],
                        'process_name' => $s['process_name'] ?: null,
                        'factory' => $s['factory'],
                    ],
                    [
                        'skill_pct' => $s['skill_pct'],
                        'updated_by' => $user->id,
                    ]
                );
            }

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()]);
        }
    }

    // GET /api/skills  — JSON list untuk TV/slide
    public function apiList(Request $request)
    {
        $scId    = ScContext::id();
        $factory = $request->get('factory', ScContext::firstFactory());
        $shift   = $request->get('shift', 'all');

        $memberQ = \App\Models\Member::where('sc_id', $scId)
            ->where('factory', $factory)
            ->where('status', 'active')
            ->orderBy('nama');

        if ($shift !== 'all') {
            $memberQ->where('shift', $shift);
        }

        $members = $memberQ->get(['id','nama','jabatan','shift']);

        $rawSkills = MemberSkill::where('sc_id', $scId)
            ->where('factory', $factory)
            ->get();

        $skills = [];
        foreach ($rawSkills as $s) {
            $pName = $s->process_name ?: '-';
            $skills[$s->member_id][$s->machine_name][$pName] = ['skill_pct' => $s->skill_pct];
        }

        // We also need to send the machine processes structure to the TV so it knows how to build the columns
        $machineProcesses = MachineProcess::where('sc_id', $scId)
            ->where('factory', $factory)
            ->orderBy('id')
            ->get()
            ->groupBy('machine_name');

        $processes = [];
        foreach ($machineProcesses as $mName => $procs) {
            $processes[$mName] = $procs->pluck('process_name')->toArray();
        }

        return response()->json(compact('members', 'skills', 'processes'));
    }

    // POST /api/skills/processes/add
    public function addProcess(Request $request)
    {
        $request->validate([
            'factory'      => 'required|string',
            'machine_name' => 'required|string|max:100',
            'process_name' => 'required|string|max:150',
        ]);

        $user = Auth::user();
        if (!$user->isSuperAdmin() && !in_array($request->factory, (array) $user->factory)) {
            abort(403);
        }

        $process = MachineProcess::create([
            'sc_id'        => ScContext::id(),
            'factory'      => $request->factory,
            'machine_name' => $request->machine_name,
            'process_name' => $request->process_name,
        ]);

        return response()->json(['ok' => true, 'process' => $process]);
    }

    // POST /api/skills/processes/delete
    public function deleteProcess(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer|exists:machine_processes,id',
            ]);

            $process = MachineProcess::find($request->id);
            $user = Auth::user();
            if (!$user->isSuperAdmin() && !in_array($process->factory, (array) $user->factory)) {
                abort(403, 'Unauthorized factory');
            }

            // Optional: Delete all member skills for this specific process
            MemberSkill::where('sc_id', $process->sc_id)
                ->where('machine_name', $process->machine_name)
                ->where('process_name', $process->process_name)
                ->delete();

            $process->delete();
            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()]);
        }
    }
}
