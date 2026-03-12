<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Kolom yang bisa diisi mass-assignment
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Override default auth identifier ke 'username'
     * agar Auth::attempt(['username' => ...]) berfungsi.
     */
    public function getAuthIdentifierName(): string
    {
        return 'username';
    }

    // ─── Role helpers ────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSupervisor(): bool
    {
        return in_array($this->role, ['admin', 'tl', 'gl', 'pengawas']);
    }

    public function isTvOnly(): bool
    {
        return $this->role === 'tv';
    }

    /**
     * Label tampilan untuk role
     */
    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin'    => 'Administrator',
            'tl'       => 'Team Leader',
            'gl'       => 'Group Leader',
            'pengawas' => 'Pengawas',
            default    => ucfirst($this->role),
        };
    }

    /**
     * Warna badge untuk role
     */
    public function getRoleColorAttribute(): string
    {
        return match($this->role) {
            'admin'    => '#e74c3c',
            'tl'       => '#1f3c88',
            'gl'       => '#2e7d32',
            'pengawas' => '#f39c12',
            'tv'       => '#6a1b9a',
            default    => '#888',
        };
    }
}