<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/**
 * GlobalLog — Append-only audit trail for all authenticated actions.
 * Task 6 — planning.md 2026-05-20
 *
 * PII fields (user_id, username, ip) are encrypted at rest using
 * Laravel's Crypt (AES-256-CBC, keyed by APP_KEY).
 *
 * NEVER store raw IPs or usernames in plaintext columns.
 */
class GlobalLog extends Model
{
    protected $table = 'global_logs';

    /**
     * All writeable columns — encrypted PII stored in *_enc columns.
     */
    protected $fillable = [
        'user_id_enc',
        'username_enc',
        'role',
        'action',
        'target',
        'detail',
        'ip_enc',
        'user_agent',
        'factory',
    ];

    // Disable soft-deletes — append-only table
    public $timestamps = true;

    // ── Encryption helpers ──────────────────────────────────────────────────

    /**
     * Create a new log entry with automatic PII encryption.
     *
     * @param int|null    $userId
     * @param string|null $username
     * @param string|null $role
     * @param string      $action   HTTP verb or 'UI'
     * @param string      $target   Route path or event name
     * @param array|null  $detail   Extra context (will be JSON-encoded)
     * @param string|null $ip       Raw IP (will be encrypted before storage)
     * @param string|null $userAgent
     * @param string|null $factory
     */
    public static function record(
        ?int    $userId,
        ?string $username,
        ?string $role,
        string  $action,
        string  $target,
        ?array  $detail = null,
        ?string $ip = null,
        ?string $userAgent = null,
        ?string $factory = null
    ): void {
        try {
            static::create([
                'user_id_enc'  => Crypt::encryptString((string) ($userId ?? 'guest')),
                'username_enc' => Crypt::encryptString((string) ($username ?? 'unknown')),
                'role'         => $role,
                'action'       => strtoupper(substr($action, 0, 10)),
                'target'       => substr($target, 0, 255),
                'detail'       => $detail ? json_encode($detail, JSON_UNESCAPED_UNICODE) : null,
                'ip_enc'       => Crypt::encryptString((string) ($ip ?? 'unknown')),
                'user_agent'   => $userAgent ? substr($userAgent, 0, 512) : null,
                'factory'      => $factory ? substr($factory, 0, 100) : null,
            ]);
        } catch (\Throwable $e) {
            // Never let logging failure crash the application
            Log::error('[GlobalLog] Failed to write activity log: ' . $e->getMessage());
        }
    }

    // ── Decryption accessors (for display in Global Logs dashboard) ─────────

    /**
     * Safely decrypt an encrypted field, returning a fallback on error.
     */
    public static function safeDecrypt(?string $encrypted, string $fallback = '—'): string
    {
        if (!$encrypted) return $fallback;
        try {
            return Crypt::decryptString($encrypted);
        } catch (\Throwable) {
            return '[encrypted]';
        }
    }

    /**
     * Get decrypted username (for display only — never return raw from API).
     */
    public function getDecryptedUsernameAttribute(): string
    {
        return static::safeDecrypt($this->username_enc);
    }

    /**
     * Get decrypted IP (for display only — never return raw from API).
     */
    public function getDecryptedIpAttribute(): string
    {
        return static::safeDecrypt($this->ip_enc);
    }

    /**
     * Get decrypted user ID (for display only).
     */
    public function getDecryptedUserIdAttribute(): string
    {
        return static::safeDecrypt($this->user_id_enc);
    }
}
