<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * SC PIN Protection — /admin/scpin
 *
 * Each SC admin can enable a PIN. When enabled, any user switching INTO
 * this SC via the SC Switcher is challenged for that PIN first.
 */
class ScPinController extends Controller
{
    /** GET /admin/scpin — config page (superadmin only) */
    public function index()
    {
        $scs = Sc::orderBy('order_index')->get(['id', 'name', 'short_label', 'require_pin']);
        return view('admin.scpin.index', compact('scs'));
    }

    /** POST /admin/scpin/{sc}/toggle — enable/disable PIN for an SC */
    public function toggle(Request $request, Sc $sc)
    {
        $request->validate([
            'require_pin' => 'required|boolean',
            'pin' => 'nullable|string|min:4|max:12',
        ]);

        $data = ['require_pin' => (bool) $request->require_pin];

        if ($request->require_pin && $request->filled('pin')) {
            $data['pin_hash'] = Hash::make($request->pin);
        }

        if (!$request->require_pin) {
            $data['pin_hash'] = null; // ponytail: clear hash when protection disabled
        }

        $sc->update($data);

        return response()->json(['ok' => true, 'require_pin' => $sc->fresh()->require_pin]);
    }

    /** POST /admin/scpin/verify — verify PIN before SC switch */
    public function verify(Request $request)
    {
        $request->validate([
            'sc_id' => 'required|integer|exists:scs,id',
            'pin' => 'required|string',
        ]);

        $sc = Sc::findOrFail($request->sc_id);

        if (!$sc->require_pin) {
            return response()->json(['ok' => true]); // no pin required
        }

        if (!$sc->pin_hash || !Hash::check($request->pin, $sc->pin_hash)) {
            return response()->json(['ok' => false, 'message' => 'PIN salah. Akses ditolak.'], 403);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * GET /admin/scpin/status — returns which SCs require PIN
     * Used by switchSc() JS to know whether to show the challenge.
     */
    public function status()
    {
        $scs = Sc::orderBy('id')->get(['id', 'require_pin']);
        $map = $scs->pluck('require_pin', 'id'); // {id: bool}
        return response()->json($map);
    }
}
