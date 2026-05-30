<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsenceReason;
use Illuminate\Http\Request;

class AbsenceReasonController extends Controller
{
    public function index()
    {
        $scId = auth()->user()->sc_id ?? 1;
        $reasons = AbsenceReason::where('sc_id', $scId)->orderBy('name')->get();
        return view('admin.absence_reasons.index', compact('reasons'));
    }

    public function store(Request $request)
    {
        $scId = auth()->user()->sc_id ?? 1;
        $request->validate([
            'name' => "required|string|unique:absence_reasons,name,NULL,id,sc_id,{$scId}",
            'color' => 'required|string',
        ]);

        AbsenceReason::create([
            'sc_id' => $scId,
            'name' => $request->name,
            'color' => $request->color,
        ]);

        return back()->with('success', 'Alasan absen berhasil ditambahkan');
    }

    public function update(Request $request, AbsenceReason $absenceReason)
    {
        $scId = auth()->user()->sc_id ?? 1;
        $request->validate([
            'name' => "required|string|unique:absence_reasons,name,{$absenceReason->id},id,sc_id,{$scId}",
            'color' => 'required|string',
        ]);

        $absenceReason->update($request->only(['name', 'color']));

        return back()->with('success', 'Alasan absen berhasil diperbarui');
    }

    public function destroy(AbsenceReason $absenceReason)
    {
        // Don't allow deleting default reasons if needed, but let's be flexible
        $absenceReason->delete();

        return back()->with('success', 'Alasan absen berhasil dihapus');
    }
}
