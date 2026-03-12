<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('factory');
            $table->string('name');          // nama mesin, e.g. "#01-2500T"
            $table->string('photo')->nullable(); // path foto, e.g. "machines/abc123.jpg"
            $table->timestamps();

            $table->unique(['factory', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
