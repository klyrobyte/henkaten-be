<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factory_layouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sc_id');
            $table->string('factory');
            $table->string('layout_image')->nullable(); // path to uploaded image
            $table->integer('layout_width')->nullable();
            $table->integer('layout_height')->nullable();
            $table->timestamps();

            $table->unique(['sc_id', 'factory']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factory_layouts');
    }
};
