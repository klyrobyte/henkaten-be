<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignmentReplacement;
use App\Models\Member;
use App\Models\AbsenceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Replacement
 * 
 * APIs for managing Replacement.
 */
class ReplacementController extends Controller
{
    /**
     * Simpan pengganti yang dipilih
     * POST /admin/replacements
     * develop by rizky
     * 
     * FIXED: Handle mesin_secondary  - jika member yang di-replace punya mesin_secondary,
     * create replacement records untuk KEDUA mesin, bukan hanya target_machine.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'factory' => 'required|string',
            'shift' => 'required|in:A,B',
            'target_machine' => 'required|string',
            'member_id' => 'required|exists:members,id',
        ]);

        $user = Auth::user();
        $scId = $user->sc_id ?? 1;
        $factory = $request->factory;

        if ($user && !$user->isSuperAdmin()) {
            $allowedFactories = (array) $user->factory;
            if (!in_array($factory, $allowedFactories)) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized factory access.'], 403);
            }
        }

        $replacementMember = Member::where('sc_id', $scId)->find($request->member_id);
        $replacementSourceMachine = $replacementMember?->mesin ?? null;

        $originalMember = Member::where('sc_id', $scId)
            ->where('factory', $request->factory)
            ->where('shift', $request->shift)
            ->where(function ($q) use ($request) {
                $q->where('mesin', $request->target_machine)
                    ->orWhere('mesin_secondary', $request->target_machine);
            })
            ->first();

        $targetMachines = [$request->target_machine];

        if ($originalMember) {
            $hasPrimary = !empty($originalMember->mesin);
            $hasSecondary = !empty($originalMember->mesin_secondary);

            if ($hasPrimary && $hasSecondary) {
                $pairedMachine = ($request->target_machine === $originalMember->mesin)
                    ? $originalMember->mesin_secondary
                    : $originalMember->mesin;

                if (!in_array($pairedMachine, $targetMachines)) {
                    $targetMachines[] = $pairedMachine;
                }
            }
        }  

        $createdReplacements = [];

        foreach ($targetMachines as $targetMachine) {
            $replacement = AssignmentReplacement::updateOrCreate(
                [
                    'sc_id' => $scId,
                    'tanggal' => $request->tanggal,
                    'factory' => $request->factory,
                    'shift' => $request->shift,
                    'member_id' => $request->member_id,
                    'target_machine' => $targetMachine,
                ],
                ['source_machine' => $replacementSourceMachine]
            );
            $createdReplacements[] = $replacement;
        }

        return response()->json([
            'ok' => true,
            'replacements' => $createdReplacements,
            'member_name' => $replacementMember?->nama,
            'target_machines' => $targetMachines,
        ]);
    }

    /**
     * Ambil pengganti aktif (hanya yang member aslinya masih absen)
     * GET /admin/replacements?tanggal=&factory=&shift=
     *
     * Sekaligus auto-cleanup: hapus dari DB jika member asli sudah hadir.
     * "Member asli" = member yang shift/factory-nya sama dengan target_machine
     * dan statusnya sudah tidak absen di absence_records.
     * Fixed bug by Rizky
     */
    public function index(Request $request)
    {
        $tanggal = $request->tanggal;
        $factory = $request->factory;
        $shift = $request->shift;

        $user = Auth::user();
        $scId = $user->sc_id ?? 1;

        if ($user && !$user->isSuperAdmin()) {
            $allowedFactories = (array) $user->factory;
            if (!empty($allowedFactories) && !in_array($factory, $allowedFactories)) {
                $factory = $allowedFactories[0];
            }
        }

        $replacements = AssignmentReplacement::where('sc_id', $scId)
            ->with('member')
            ->where([
                'tanggal' => $tanggal,
                'factory' => $factory,
                'shift' => $shift,
            ])
            ->get();

        // Auto-cleanup: cek setiap pengganti, apakah ada member asli
        // (yang mesinnya = target_machine, atau mesin_secondary = target_machine) yang sudah tidak absen lagi
        $toDelete = [];

        foreach ($replacements as $r) {
            // Cari member asli yang operate target_machine (either primary or secondary)
            // Exclude si pengganti itu sendiri (id != member_id)
            $originalMembers = Member::where('sc_id', $scId)
                ->where('factory', $factory)
                ->where('shift', $shift)
                ->where('id', '!=', $r->member_id)
                ->where(function ($q) use ($r) {
                    // Check both mesin dan mesin_secondary
                    $q->where('mesin', $r->target_machine)
                        ->orWhere('mesin_secondary', $r->target_machine);
                })
                ->pluck('id');

            if ($originalMembers->isEmpty())
                continue;

            // Cek apakah SEMUA member asli sudah hadir (tidak ada yg absen)
            $stillAbsen = AbsenceRecord::where('sc_id', $scId)
                ->where('tanggal', $tanggal)
                ->where('shift', $shift)
                ->whereIn('member_id', $originalMembers)
                ->where('status', 'absen')
                ->exists();

            if (!$stillAbsen) {
                // Member asli sudah hadir → hapus record pengganti ini
                $toDelete[] = $r->id;
            }
        }

        if (!empty($toDelete)) {
            AssignmentReplacement::whereIn('id', $toDelete)->delete();
            // Refresh collection setelah cleanup
            $replacements = $replacements->whereNotIn('id', $toDelete)->values();
        }

        return response()->json(
            $replacements->map(fn($r) => [
                'id' => $r->id,
                'member_id' => $r->member_id,
                'member_name' => $r->member?->nama,
                'member_photo' => $r->member?->photo_url,
                'target_machine' => $r->target_machine,
                'source_machine' => $r->source_machine,
            ])->values()
        );
    }

    /**
     * Hapus pengganti manual (tombol batalkan)
     * DELETE /admin/replacements/{id}
     */
    public function destroy(AssignmentReplacement $replacement)
    {
        $user = Auth::user();
        $scId = $user->sc_id ?? 1;

        if ($replacement->sc_id != $scId) {
             abort(403, 'Unauthorized SC access.');
        }

        if ($user && !$user->isSuperAdmin()) {
            $allowedFactories = (array) $user->factory;
            if (!in_array($replacement->factory, $allowedFactories)) {
                return response()->json(['ok' => false, 'message' => 'Unauthorized factory access.'], 403);
            }
        }
        $replacement->delete();
        return response()->json(['ok' => true]);
    }
}
