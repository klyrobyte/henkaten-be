<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factory_id')->constrained('factories')->cascadeOnDelete();
            $table->string('name');                         // display title e.g. "Resin Injection"
            $table->string('code');                         // matches machines.section e.g. "f2-resin"
            $table->boolean('is_key_persons')->default(false);
            $table->boolean('is_key_robot')->default(false);
            $table->integer('order_index')->default(0);
            $table->timestamps();

            $table->unique(['factory_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
