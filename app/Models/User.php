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
        'sc_id',
        'name',
        'username',
        'password',
        'role',
        'factory',
        'shift',
    ];

    public function sc()
    {
        return $this->belongsTo(Sc::class, 'sc_id');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'factory' => 'array',
    ];

    /**
     * Override default auth identifier ke 'username'
     * agar Auth::attempt(['username' => ...]) berfungsi.
     */
    public function getAuthIdentifierName(): string
    {
        return 'username';
    }

    //  ─ Role helpers                         

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function getActiveScId(): int
    {
        if ($this->isSuperAdmin()) {
            return (int) session('active_sc_id', $this->sc_id ?? 1);
        }
        return (int) ($this->sc_id ?? 1);
    }

    public function getScIdAttribute($value)
    {
        if ($this->isSuperAdmin() && !app()->runningInConsole() && request()->hasSession()) {
            return (int) session('active_sc_id', $value ?? 1);
        }
        return (int) ($value ?? 1);
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['superadmin', 'admin']);
    }

    public function isSupervisor(): bool
    {
        return in_array($this->role, ['superadmin', 'admin', 'tl', 'gl', 'pengawas']);
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
        return match ($this->role) {
            'superadmin' => 'Super Admin',
            'admin' => 'Administrator',
            'tl' => 'Team Leader',
            'gl' => 'Group Leader',
            'pengawas' => 'Pengawas',
            default => ucfirst($this->role),
        };
    }

    /**
     * Warna badge untuk role
     */
    public function getRoleColorAttribute(): string
    {
        return match ($this->role) {
            'superadmin' => '#000000',
            'admin' => '#e74c3c',
            'tl' => '#1f3c88',
            'gl' => '#2e7d32',
            'pengawas' => '#f39c12',
            'tv' => '#6a1b9a',
            default => '#888',
        };
    }
}
