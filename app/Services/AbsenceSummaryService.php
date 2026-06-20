<?php

namespace App\Services;

use App\Models\AbsenceRecord;
use App\Models\AbsenceSummary;
use App\Models\Member;

/**
 * AbsenceSummaryService
 *
 * Menghitung breakdown kehadiran dari AbsenceRecord ke AbsenceSummary.
 *
 * LOGIKA UTAMA:
 * ─────────────────────────────────────────────────────────────────
 * Setiap member yang absen HANYA masuk ke SATU bucket saja:
 *   - Cek jabatan → Operator atau SPV/Pengawas?
 *   - Cek reason  → cuti / sakit / ijin / Alpha / null
 *
 * mp_absen = TOTAL semua yang absen (untuk Man summary di dashboard)
 * Diagram detail = op_* + spv_* (tidak ada double count)
 *
 * JABATAN MAPPING:
 *   SPV/Pengawas = jabatan mengandung: GL, TL, KY, SPV, Supervisor, Pengawas, Leader, QC
 *   Operator     = semua jabatan lainnya
 */
class AbsenceSummaryService
{
    // Keyword jabatan yang dianggap SPV / Pengawas (case-insensitive)
    private const SPV_KEYWORDS = ['GL', 'TL', 'KY', 'SPV', 'Supervisor', 'Pengawas', 'Leader', 'QC'];

    /**
     * Hitung dan simpan summary untuk tanggal/factory/shift tertentu.
     * Dipanggil setiap kali absensi di-save/sync.
     */
    public function recalculate(string $tanggal, string $factory, string $shift, ?int $scId = null): AbsenceSummary
    {
        $scId = $scId ?? (auth()->check() ? (auth()->user()->sc_id ?? 1) : 1);

        // Ambil semua member aktif untuk shift ini
        $members = Member::where('sc_id', $scId)
            ->where('factory', $factory)
            ->where('shift', $shift)
            ->where('status', 'active')
            ->get()
            ->keyBy('id');

        $totalMember = $members->count();

        // Ambil semua AbsenceRecord untuk hari ini
        $records = AbsenceRecord::where([
            'sc_id' => $scId,
            'tanggal' => $tanggal,
            'factory' => $factory,
            'shift' => $shift,
            'status' => 'absen',
        ])->get();

        // Init counter
        $opHadir = $opCuti = $opSakit = $opIjin = $opAlpha = 0;
        $spvHadir = $spvCuti = $spvSakit = $spvIjin = $spvAlpha = 0;

        foreach ($records as $rec) {
            $member = $members[$rec->member_id] ?? null;
            $jabatan = $member?->jabatan ?? '';
            $reason = strtolower($rec->reason ?? 'alpha');
            $isSpv = $this->isSpv($jabatan);

            if ($isSpv) {
                match ($reason) {
                    'cuti' => $spvCuti++,
                    'sakit' => $spvSakit++,
                    'ijin' => $spvIjin++,
                    'izin' => $spvIjin++,
                    default => $spvAlpha++,
                };
            } else {
                match ($reason) {
                    'cuti' => $opCuti++,
                    'sakit' => $opSakit++,
                    'ijin' => $opIjin++,
                    'izin' => $opIjin++,
                    default => $opAlpha++,
                };
            }
        }

        $mpAbsen = $records->count(); // total semua absen
        $mpHadir = $totalMember - $mpAbsen;

        // Hitung hadir per grup
        $totalSpv = $members->filter(fn($m) => $this->isSpv($m->jabatan ?? ''))->count();
        $totalOp = $totalMember - $totalSpv;
        $spvAbsen = $spvCuti + $spvSakit + $spvIjin + $spvAlpha;
        $opAbsen = $opCuti + $opSakit + $opIjin + $opAlpha;
        $spvHadirV = $totalSpv - $spvAbsen;
        $opHadirV = $totalOp - $opAbsen;

        return AbsenceSummary::updateOrCreate(
            ['sc_id' => $scId, 'tanggal' => $tanggal, 'factory' => $factory, 'shift' => $shift],
            [
                'total_member' => $totalMember,
                'mp_hadir' => max(0, $mpHadir),
                'mp_absen' => $mpAbsen,
                'total_absen' => $mpAbsen,

                'op_hadir' => max(0, $opHadirV),
                'op_cuti' => $opCuti,
                'op_sakit' => $opSakit,
                'op_ijin' => $opIjin,
                'op_Alpha' => $opAlpha,

                'spv_hadir' => max(0, $spvHadirV),
                'spv_cuti' => $spvCuti,
                'spv_sakit' => $spvSakit,
                'spv_ijin' => $spvIjin,
                'spv_Alpha' => $spvAlpha,
            ]
        );
    }

    /**
     * Cek apakah jabatan ini termasuk SPV / Pengawas
     */
    public function isSpv(string $jabatan): bool
    {
        $jabatanUpper = strtoupper($jabatan);
        foreach (self::SPV_KEYWORDS as $kw) {
            if (str_contains($jabatanUpper, strtoupper($kw))) {
                return true;
            }
        }
        return false;
    }
}
