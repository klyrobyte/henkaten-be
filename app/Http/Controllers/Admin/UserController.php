<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role')->orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username|alpha_dash',
            'password' => 'required|string|min:6|confirmed',
            'role'     => ['required', Rule::in(['admin', 'tl', 'gl', 'pengawas', 'tv'])],
            'factory'  => ['nullable', 'string', Rule::requiredIf(fn() => in_array($request->role, ['tl', 'gl', 'pengawas']))],
            'shift'    => ['nullable', Rule::in(['A', 'B']), Rule::requiredIf(fn() => in_array($request->role, ['tl', 'gl', 'pengawas']))],
        ], [
            'username.unique'     => 'Username sudah digunakan.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, dash, dan underscore.',
            'password.min'        => 'Password minimal 6 karakter.',
            'password.confirmed'  => 'Konfirmasi password tidak cocok.',
            'factory.required'    => 'Factory wajib diisi untuk role ini.',
            'shift.required'      => 'Shift wajib diisi untuk role ini.',
        ]);

        // Admin dan TV tidak perlu factory/shift
        $restrictedRoles = ['tl', 'gl', 'pengawas'];

        User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'factory'  => in_array($request->role, $restrictedRoles) ? $request->factory : null,
            'shift'    => in_array($request->role, $restrictedRoles) ? $request->shift    : null,
        ]);

        return response()->json(['ok' => true, 'message' => '✅ User berhasil ditambahkan.']);
    }

    public function update(Request $request, User $user)
    {
        // Cegah admin mengedit dirinya sendiri via endpoint ini (untuk keamanan role)
        $request->validate([
            'name'     => 'required|string|max:100',
            'username' => ['required','string','max:50','alpha_dash', Rule::unique('users','username')->ignore($user->id)],
            'role'     => ['required', Rule::in(['admin', 'tl', 'gl', 'pengawas', 'tv'])],
            'password' => 'nullable|string|min:6|confirmed',
            'factory'  => ['nullable', 'string', Rule::requiredIf(fn() => in_array($request->role, ['tl', 'gl', 'pengawas']))],
            'shift'    => ['nullable', Rule::in(['A', 'B']), Rule::requiredIf(fn() => in_array($request->role, ['tl', 'gl', 'pengawas']))],
        ], [
            'username.unique'     => 'Username sudah digunakan.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, dash, dan underscore.',
            'password.min'        => 'Password minimal 6 karakter.',
            'password.confirmed'  => 'Konfirmasi password tidak cocok.',
            'factory.required'    => 'Factory wajib diisi untuk role ini.',
            'shift.required'      => 'Shift wajib diisi untuk role ini.',
        ]);

        // Cegah mengubah role satu-satunya admin
        if ($user->role === 'admin' && $request->role !== 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return response()->json(['ok' => false, 'message' => '⚠️ Tidak bisa mengubah role — harus ada minimal 1 Admin.'], 422);
            }
        }

        $restrictedRoles = ['tl', 'gl', 'pengawas'];

        $data = [
            'name'     => $request->name,
            'username' => $request->username,
            'role'     => $request->role,
            'factory'  => in_array($request->role, $restrictedRoles) ? $request->factory : null,
            'shift'    => in_array($request->role, $restrictedRoles) ? $request->shift    : null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json(['ok' => true, 'message' => '✅ User berhasil diperbarui.']);
    }

    public function destroy(User $user)
    {
        // Cegah hapus diri sendiri
        if ($user->id === Auth::id()) {
            return response()->json(['ok' => false, 'message' => '⚠️ Tidak bisa menghapus akun sendiri.'], 422);
        }

        // Cegah hapus satu-satunya admin
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                return response()->json(['ok' => false, 'message' => '⚠️ Tidak bisa menghapus — harus ada minimal 1 Admin.'], 422);
            }
        }

        $user->delete();
        return response()->json(['ok' => true, 'message' => '🗑️ User berhasil dihapus.']);
    }

    public function show(User $user)
    {
        return response()->json($user->only(['id', 'name', 'username', 'role', 'factory', 'shift', 'created_at']));
    }
}