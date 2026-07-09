<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\User;
use App\Services\ScContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * @group User
 * 
 * APIs for managing User.
 */
class UserController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();
        $scId = ScContext::id();
        $query = User::where('sc_id', $scId)->orderBy('role')->orderBy('name');

        if ($currentUser->isSuperAdmin()) {
            // Superadmin sees everyone in their SC (SC1)
            $users = $query->get();
            $factories = Factory::where('sc_id', $scId)->orderBy('order_index')->get();
        } else {
            // Normal admin only sees users within their SC AND their factory scope
            $adminFactories = ScContext::allowedFactories($currentUser);
            
            $users = $query->where('role', '!=', 'superadmin')
                ->get()
                ->filter(function($u) use ($adminFactories) {
                    // If the current admin has unrestricted access, they can see all non-superadmin users
                    if (empty($adminFactories)) return true;
                    
                    // If the user being checked has unrestricted access, an admin WITH restrictions cannot see them
                    // (prevents lower-level admins from managing higher-level admins)
                    if (empty($u->factory)) return false;
                    
                    $uFactories = (array) $u->factory;
                    return !empty(array_intersect($uFactories, $adminFactories));
                });
                
            $factoriesQuery = Factory::where('sc_id', $scId)->orderBy('order_index');
            if (!empty($adminFactories)) {
                $factoriesQuery->whereIn('name', $adminFactories);
            }
            $factories = $factoriesQuery->get();
        }

        return view('admin.users.index', compact('users', 'factories'));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();
        $scId = ScContext::id();
        
        $allowedRoles = ['admin', 'cl', 'tl', 'gl', 'pengawas', 'tv']; // ponytail: cl above gl/tl
        if ($currentUser->isSuperAdmin()) {
            $allowedRoles[] = 'superadmin';
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username|alpha_dash',
            'password' => 'required|string|min:6|confirmed',
            'role' => ['required', Rule::in($allowedRoles)],
            'factory' => 'nullable|array',
            'shift' => ['nullable', Rule::in(['A', 'B', ''])],
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, dash, dan underscore.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $factory = $request->factory;
        if (!$currentUser->isSuperAdmin()) {
            // Force factory to admin's factories
            $factory = (array) $currentUser->factory;
        }

        User::create([
            'sc_id' => $scId,
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'factory' => $factory,
            'shift' => $request->shift ?: null,
        ]);

        return response()->json(['ok' => true, 'message' => '✅ User berhasil ditambahkan.']);
    }

    public function update(Request $request, User $user)
    {
        $currentUser = Auth::user();
        $scId = ScContext::id();

        // Prevent normal admin from editing superadmin or users from different SC
        if (!$currentUser->isSuperAdmin()) {
            if ($user->isSuperAdmin() || $user->sc_id != $scId) {
                return response()->json(['ok' => false, 'message' => 'Akses ditolak.'], 403);
            }
        }

        $allowedRoles = ['admin', 'cl', 'tl', 'gl', 'pengawas', 'tv']; // ponytail: cl above gl/tl
        if ($currentUser->isSuperAdmin()) {
            $allowedRoles[] = 'superadmin';
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'username' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($user->id)],
            'role' => ['required', Rule::in($allowedRoles)],
            'password' => 'nullable|string|min:6|confirmed',
            'factory' => 'nullable|array',
            'shift' => ['nullable', Rule::in(['A', 'B', ''])],
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, dash, dan underscore.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $factory = $request->factory;
        if (!$currentUser->isSuperAdmin()) {
            // Force factory to admin's factories
            $factory = (array) $currentUser->factory;
        }

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'role' => $request->role,
            'factory' => $factory,
            'shift' => $request->shift ?: null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json(['ok' => true, 'message' => '✅ User berhasil diperbarui.']);
    }

    public function destroy(User $user)
    {
        $currentUser = Auth::user();
        $scId = ScContext::id();

        // Prevent normal admin from deleting superadmin or users from different SC
        if (!$currentUser->isSuperAdmin()) {
            if ($user->isSuperAdmin() || $user->sc_id != $scId) {
                return response()->json(['ok' => false, 'message' => 'Akses ditolak.'], 403);
            }
        }

        // Prevent deleting self
        if ($user->id === Auth::id()) {
            return response()->json(['ok' => false, 'message' => '⚠️ Tidak bisa menghapus akun sendiri.'], 422);
        }

        $user->delete();
        return response()->json(['ok' => true, 'message' => '🗑️ User berhasil dihapus.']);
    }

    public function show(User $user)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isSuperAdmin() && $user->isSuperAdmin()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        return response()->json($user->only(['id', 'name', 'username', 'role', 'factory', 'shift', 'created_at']));
    }
}
