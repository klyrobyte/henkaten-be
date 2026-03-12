<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom role jika belum ada
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'tl', 'gl', 'pengawas'])
                      ->default('pengawas')
                      ->after('username');
            }
        });

        // Set semua user existing ke admin agar tidak kehilangan akses
        User::whereNull('role')->orWhere('role', '')->update(['role' => 'admin']);

        // Buat akun admin default jika belum ada user sama sekali
        if (User::count() === 0) {
            User::create([
                'name'     => 'Administrator',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
