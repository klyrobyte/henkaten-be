<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();               // e.g. "Factory 2"
            $table->string('slug')->unique();               // e.g. "factory-2"
            $table->string('short_label')->default('');     // e.g. "F2"
            $table->string('gradient')->default('linear-gradient(135deg,#2e7d32,#43a047)'); // TV card color
            $table->integer('order_index')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factories');
    }
};
