<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['username', 'password', 'role', 'name'];
    protected $hidden = ['password', 'remember_token'];

    // ── Ubah identifier dari 'email' → 'username' ───────────────────
    // Dibutuhkan agar Auth::attempt(['username' => ...]) berfungsi benar
    public function getAuthIdentifierName(): string
    {
        return 'username';
    }

    // ── Penting: Laravel's GenericUser/SessionGuard mencari via
    //    retrieveByCredentials yang default-nya pakai semua key kecuali 'password'.
    //    Karena kita pakai 'username', ini sudah cukup  - TAPI kita juga harus
    //    override getAuthPassword agar hash-check jalan.
    public function getAuthPassword(): string
    {
        return $this->password;
    }
}
