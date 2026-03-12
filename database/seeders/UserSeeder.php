<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder
 *
 * Menggantikan array adminUsers yang hardcoded di henkatenadmin.html:
 *   const adminUsers = [
 *     {username:"admin",   password:"...", role:"superadmin"},
 *     {username:"sugity",  password:"...", role:"admin"},
 *     {username:"factory", password:"...", role:"operator"},
 *   ];
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'username' => 'admin',
                'password' => Hash::make('admin123'),   // ganti sebelum production!
                'role'     => 'superadmin',
                'name'     => 'Super Administrator',
            ],
            [
                'username' => 'sugity',
                'password' => Hash::make('sugity123'),  // ganti sebelum production!
                'role'     => 'admin',
                'name'     => 'Sugity Administrator',
            ],
            [
                'username' => 'factory',
                'password' => Hash::make('factory123'), // ganti sebelum production!
                'role'     => 'operator',
                'name'     => 'Factory Operator',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['username' => $user['username']], $user);
        }
    }
}
