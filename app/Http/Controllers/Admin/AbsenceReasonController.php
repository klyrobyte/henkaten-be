<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsenceReason;
use Illuminate\Http\Request;

class AbsenceReasonController extends Controller
{
    public function index()
    {
        $reasons = AbsenceReason::orderBy('name')->get();
        return view('admin.absence_reasons.index', compact('reasons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:absence_reasons,name',
            'color' => 'required|string',
        ]);

        AbsenceReason::create($request->only(['name', 'color']));

        return back()->with('success', 'Alasan absen berhasil ditambahkan');
    }

    public function update(Request $request, AbsenceReason $absenceReason)
    {
        $request->validate([
            'name' => 'required|string|unique:absence_reasons,name,' . $absenceReason->id,
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
