<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignmentReplacement;
use App\Models\Member;
use App\Models\AbsenceRecord;
use Illuminate\Http\Request;

class ReplacementController extends Controller
{
    /**
     * Simpan pengganti yang dipilih
     * POST /admin/replacements
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'        => 'required|date',
            'factory'        => 'required|string',
            'shift'          => 'required|in:A,B',
            'target_machine' => 'required|string',
            'member_id'      => 'required|exists:members,id',
        ]);

        $member        = Member::find($request->member_id);
        $sourceMachine = $member?->mesin ?? null;

        $replacement = AssignmentReplacement::updateOrCreate(
            [
                'tanggal'   => $request->tanggal,
                'factory'   => $request->factory,
                'shift'     => $request->shift,
                'member_id' => $request->member_id,
            ],
            [
                'target_machine' => $request->target_machine,
                'source_machine' => $sourceMachine,
            ]
        );

        return response()->json([
            'ok'          => true,
            'replacement' => $replacement,
            'member_name' => $member?->nama,
        ]);
    }

    /**
     * Ambil pengganti aktif (hanya yang member aslinya masih absen)
     * GET /admin/replacements?tanggal=&factory=&shift=
     *
     * Sekaligus auto-cleanup: hapus dari DB jika member asli sudah hadir.
     * "Member asli" = member yang shift/factory-nya sama dengan target_machine
     * dan statusnya sudah tidak absen di absence_records.
     */
    public function index(Request $request)
    {
        $tanggal = $request->tanggal;
        $factory = $request->factory;
        $shift   = $request->shift;

        $replacements = AssignmentReplacement::with('member')
            ->where([
                'tanggal' => $tanggal,
                'factory' => $factory,
                'shift'   => $shift,
            ])
            ->get();

        // Auto-cleanup: cek setiap pengganti, apakah ada member asli
        // (yang mesinnya = target_machine) yang sudah tidak absen lagi
        $toDelete = [];

        foreach ($replacements as $r) {
            // Cari member asli di target_machine (bukan si pengganti itu sendiri)
            $originalMembers = Member::where('mesin', $r->target_machine)
                ->where('factory', $factory)
                ->where('shift', $shift)
                ->where('id', '!=', $r->member_id)
                ->pluck('id');

            if ($originalMembers->isEmpty()) continue;

            // Cek apakah SEMUA member asli sudah hadir (tidak ada yg absen)
            $stillAbsen = AbsenceRecord::where('tanggal', $tanggal)
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
                'id'             => $r->id,
                'member_id'      => $r->member_id,
                'member_name'    => $r->member?->nama,
                'member_photo'   => $r->member?->photo_url,
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
        $replacement->delete();
        return response()->json(['ok' => true]);
    }
}