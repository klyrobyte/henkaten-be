<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();    // e.g. "mesin"  - matches machines.status
            $table->string('label');            // e.g. "Mesin"
            $table->string('icon')->default('⚙️');
            $table->string('color')->default('#1f3c88');
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
