<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * ══ NonShiftResolver — Resolver Shift Aktif untuk Member Non-Shift (NS) ══════
 *
 * Member Non-Shift (NS) tidak terikat pada shift A atau B secara permanen.
 * Mereka mengikuti shift yang sedang berjalan berdasarkan rotasi mingguan:
 *   • Seminggu Shift A  →  seminggu Shift B  →  dst.
 *
 * CARA KERJA ROTASI:
 * ──────────────────
 * Gunakan tanggal referensi (epoch) sebagai "minggu ke-0 Shift A".
 * Hitung selisih minggu dari epoch ke tanggal yang diminta.
 * Jika selisih minggu genap  → Shift A
 * Jika selisih minggu ganjil → Shift B
 *
 * Minggu dimulai pada SENIN (ISO week standard).
 *
 * TANGGAL REFERENSI:
 * ──────────────────
 * Default: 2026-06-23 (Senin) = awal Shift A.
 * Dapat diubah melalui env: NS_SHIFT_EPOCH=2026-06-23
 * Dapat diubah melalui env: NS_SHIFT_START=A (shift di epoch, default 'A')
 *
 * @author Rizky Daffy
 */
class NonShiftResolver
{
    /**
     * Tanggal referensi epoch (Senin = awal minggu rotasi pertama).
     * Dapat di-override via .env: NS_SHIFT_EPOCH=YYYY-MM-DD
     */
    private static function epoch(): Carbon
    {
        $envEpoch = env('NS_SHIFT_EPOCH', '2026-06-23');
        return Carbon::parse($envEpoch)->startOfWeek(Carbon::MONDAY);
    }

    /**
     * Shift di minggu epoch (default 'A').
     * Dapat di-override via .env: NS_SHIFT_START=A
     */
    private static function epochShift(): string
    {
        return strtoupper(env('NS_SHIFT_START', 'A'));
    }

    /**
     * Tentukan shift aktif (A atau B) untuk tanggal tertentu.
     *
     * @param  string|\Carbon\Carbon|null  $tanggal  Format: 'Y-m-d' atau Carbon. Default: today.
     * @return string  'A' atau 'B'
     */
    public static function activeShiftFor(string|Carbon|null $tanggal = null): string
    {
        $date = $tanggal
            ? (is_string($tanggal) ? Carbon::parse($tanggal) : $tanggal)
            : Carbon::today();

        $epoch = static::epoch();
        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);

        // Jumlah minggu dari epoch ke minggu tanggal target
        $weeksDiff = (int) $epoch->diffInWeeks($weekStart);

        // Jika epoch ada di masa depan (selisih negatif), diffInWeeks tetap positif
        // karena Carbon selalu mengembalikan nilai absolut. Kita perlu cek arah.
        if ($weekStart->lt($epoch)) {
            // Sebelum epoch — rotasi berjalan mundur
            $weeksDiff = -((int) $weekStart->diffInWeeks($epoch));
        }

        $epochShift = static::epochShift(); // 'A' atau 'B'

        // Jika weeksDiff genap → shift sama dengan epochShift
        // Jika weeksDiff ganjil → shift berlawanan
        $isEven = ($weeksDiff % 2 === 0);

        if ($epochShift === 'A') {
            return $isEven ? 'A' : 'B';
        } else {
            return $isEven ? 'B' : 'A';
        }
    }

    /**
     * Helper untuk query: kembalikan array shift reguler + NS (jika aktif)
     */
    public static function shiftsFor(string $shift, ?string $tanggal = null): array
    {
        // @rizkydaffy: user requested NS members to unconditionally exist on both shifts (bypassing rotation)
        return [$shift, 'NS'];
    }
}
