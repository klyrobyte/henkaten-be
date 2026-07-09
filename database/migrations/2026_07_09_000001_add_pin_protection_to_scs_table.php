<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scs', function (Blueprint $table) {
            $table->boolean('require_pin')->default(false)->after('detail_departemen');
            $table->string('pin_hash', 255)->nullable()->after('require_pin');
        });
    }

    public function down(): void
    {
        Schema::table('scs', function (Blueprint $table) {
            $table->dropColumn(['require_pin', 'pin_hash']);
        });
    }
};
