<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Global Activity Logs table — Task 6
 *
 * PII fields (username, ip) are stored ENCRYPTED with Laravel Crypt (AES-256-CBC).
 * user_id is stored as encrypted string to avoid exposing numeric IDs.
 * No delete/truncate endpoint is exposed — append-only by design.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('global_logs', function (Blueprint $table) {
            $table->id();
            // PII — encrypted with Crypt::encryptString() (Laravel AES-256-CBC, APP_KEY)
            $table->text('user_id_enc')->comment('Encrypted user ID');
            $table->text('username_enc')->comment('Encrypted username');
            // Non-PII metadata
            $table->string('role', 30)->nullable();
            $table->string('action', 10)->nullable();       // GET, POST, PATCH, DELETE, UI
            $table->string('target', 255)->nullable();      // Route path or UI event name
            $table->text('detail')->nullable();             // JSON-encoded extra context
            // PII — encrypted
            $table->text('ip_enc')->comment('Encrypted IP address');
            $table->string('user_agent', 512)->nullable();
            $table->string('factory', 100)->nullable();
            $table->timestamps();                           // created_at used as log timestamp
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('global_logs');
    }
};
