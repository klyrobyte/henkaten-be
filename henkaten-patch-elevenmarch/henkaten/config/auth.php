<?php
/**
 * CATATAN SETUP AUTH
 * ──────────────────
 * File ini adalah PATCH untuk config/auth.php yang sudah ada di project Laravel.
 * Ubah bagian 'providers' → 'users' menjadi seperti di bawah.
 *
 * Alasan: Project asal menggunakan username (bukan email) untuk login.
 */

// Di config/auth.php, ubah bagian ini:
return [

    'defaults' => [
        'guard'     => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\User::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
