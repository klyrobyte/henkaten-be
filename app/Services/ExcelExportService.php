<?php
// ═══════════════════════════════════════════════════════════════════════════
// FILE: app/Services/ExcelExportService.php
//
// Requires: composer require phpoffice/phpspreadsheet
// Template: henkaten_template.xlsx (3 sheets: Rekap Absen, Problem Log 3M, Dashboard)
// ═══════════════════════════════════════════════════════════════════════════

namespace App\Services;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ExcelExportService
{
    // ── Palette (exact dari template) ────────────────────────────────────
    const NAVY        = 'FF1F3C88';
    const NAVY_MID    = 'FF2C4A9E';
    const NAVY_LIGHT  = 'FFEEF1FA';
    const GREEN_DARK  = 'FF2E7D32';
    const GREEN_LIGHT = 'FFE8F5E9';
    const RED_DARK    = 'FFC62828';
    const RED_SOFT    = 'FFFFEBEE';
    const ORANGE      = 'FFE65100';
    const ORANGE_SOFT = 'FFFFF3E0';
    const BLUE_DARK   = 'FF1565C0';
    const BLUE_SOFT   = 'FFE3F2FD';
    const PURPLE_DARK = 'FF7B1FA2';
    const PURPLE_SOFT = 'FFF3E5F5';
    const DARK_GREY   = 'FF37474F';
    const MID_GREY    = 'FF757575';
    const WHITE       = 'FFFFFFFF';
    const GREY_LIGHT  = 'FFF5F5F5';
    const BLACK       = 'FF000000';

    // ═══════════════════════════════════════════════════════════════════════
    //  ENTRY POINT
    // ═══════════════════════════════════════════════════════════════════════
    public function buildFullReport(
        string     $factory,
        string     $shift,
        string     $tanggal,
        Collection $members,
        Collection $records,        // AbsenceRecord keyed by member_id
        Collection $logs,           // ProblemLog
        Collection $replacements,
    ): string {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $tanggalFormatted = Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM YYYY');
        $meta = compact('factory', 'shift', 'tanggal', 'tanggalFormatted');

        $this->buildRekapAbsen($spreadsheet, $meta, $members, $records);
        $this->buildProblemLog($spreadsheet, $meta, $logs);
        $this->buildDashboard($spreadsheet, $meta, $members, $records, $logs);

        // Aktifkan sheet Dashboard saat dibuka
        $spreadsheet->setActiveSheetIndex(2);

        $dir = storage_path('app/temp');
        if (! is_dir($dir)) mkdir($dir, 0755, true);

        $path = $dir . "/report_{$factory}_Shift{$shift}_{$tanggal}_" . uniqid() . ".xlsx";
        (new Xlsx($spreadsheet))->save($path);

        return $path;
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  SHEET 1 — REKAP ABSEN
    // ═══════════════════════════════════════════════════════════════════════
    private function buildRekapAbsen(
        Spreadsheet $wb,
        array $meta,
        Collection $members,
        Collection $records,
    ): void {
        $ws = $wb->createSheet(0);
        $ws->setTitle('Rekap Absen');
        $ws->setShowGridlines(false);

        // Column widths
        $ws->getColumnDimension('A')->setWidth(5);
        $ws->getColumnDimension('B')->setWidth(28);
        $ws->getColumnDimension('C')->setWidth(14);
        $ws->getColumnDimension('D')->setWidth(22);
        $ws->getColumnDimension('E')->setWidth(8);
        $ws->getColumnDimension('F')->setWidth(16);
        $ws->getColumnDimension('G')->setWidth(18);
        $ws->getColumnDimension('H')->setWidth(12);
        $ws->getColumnDimension('I')->setWidth(14);
        $ws->getColumnDimension('J')->setWidth(20);

        // Row heights
        foreach ([1=>42, 2=>21.75, 3=>9.75, 4=>15.75, 5=>27.75, 6=>9.75,
                  7=>18, 8=>13.5, 9=>7.5, 10=>19.5, 11=>6, 12=>21.75, 13=>27.75] as $r => $h) {
            $ws->getRowDimension($r)->setRowHeight($h);
        }

        // ROW 1: Header utama
        $ws->mergeCells('A1:J1');
        $this->s($ws, 'A1', 'HENKATEN BOARD', self::NAVY, self::WHITE, 20, true);

        // ROW 2: Subtitle
        $ws->mergeCells('A2:J2');
        $this->s($ws, 'A2',
            "REKAP ABSENSI  |  {$meta['factory']}  |  Shift {$meta['shift']}  |  {$meta['tanggalFormatted']}",
            self::NAVY_MID, 'FFCCCCCC', 11, false);

        // ROW 3: spacer
        $ws->mergeCells('A3:J3');
        $this->s($ws, 'A3', null, self::NAVY_LIGHT, self::WHITE, 9, false);

        // Hitung stats
        $totalMembers = $members->count();
        $absenCount   = $members->filter(fn($m) => $this->isAbsen($m, $records))->count();
        $hadirCount   = $totalMembers - $absenCount;
        $pctHadir     = $totalMembers ? round($hadirCount / $totalMembers * 100, 1) . '%' : '-%';
        $cutiCount    = $this->countAlasan($members, $records, 'Cuti');
        $sakitCount   = $this->countAlasan($members, $records, 'Sakit');
        $ijinCount    = $this->countAlasan($members, $records, 'Ijin');
        $mangkirCount = $this->countAlasan($members, $records, 'Mangkir');

        // ROWS 4-8: Stat cards
        $statCards = [
            ['A', 'B', $totalMembers, 'TOTAL ANGGOTA', self::NAVY_LIGHT,  self::NAVY],
            ['C', 'D', $hadirCount,   '✓ HADIR',       self::GREEN_LIGHT, self::GREEN_DARK],
            ['E', 'F', $absenCount,   '✗ TIDAK HADIR', self::RED_SOFT,    self::RED_DARK],
            ['G', 'H', $pctHadir,    '% KEHADIRAN',   self::ORANGE_SOFT, self::ORANGE],
            ['I', 'J', $cutiCount,   'CUTI',           self::BLUE_SOFT,   self::BLUE_DARK],
        ];
        foreach ($statCards as [$c1, $c2, $val, $label, $bg, $accent]) {
            $ws->mergeCells("{$c1}4:{$c2}6");
            $this->s($ws, "{$c1}4", $val, $bg, $accent, 20, true);
            $ws->mergeCells("{$c1}7:{$c2}8");
            $this->s($ws, "{$c1}7", $label, $bg, self::MID_GREY, 9, true);
        }

        // ROW 10: Alasan breakdown
        $alasanCards = [
            ['A', 'B', "{$cutiCount}  Cuti",      self::BLUE_SOFT,   self::BLUE_DARK],
            ['C', 'D', "{$sakitCount}  Sakit",    self::ORANGE_SOFT, self::ORANGE],
            ['E', 'F', "{$ijinCount}  Ijin",      self::PURPLE_SOFT, self::PURPLE_DARK],
            ['G', 'H', "{$mangkirCount}  Mangkir", self::RED_SOFT,   self::RED_DARK],
        ];
        foreach ($alasanCards as [$c1, $c2, $val, $bg, $accent]) {
            $ws->mergeCells("{$c1}10:{$c2}10");
            $this->s($ws, "{$c1}10", $val, $bg, $accent, 9, true);
        }

        // ROW 12: Sub-header tabel
        $ws->mergeCells('A12:J12');
        $this->s($ws, 'A12',
            "Data Absensi: {$meta['factory']} | Shift {$meta['shift']} | Tanggal: {$meta['tanggalFormatted']}",
            self::DARK_GREY, self::WHITE, 10, true, 'left');

        // ROW 13: Column headers
        $headers = ['No', 'Nama Lengkap', 'NIK', 'Jabatan', 'Shift', 'Factory', 'Mesin', 'Status', 'Alasan', 'Catatan'];
        foreach ($headers as $i => $h) {
            $col = chr(ord('A') + $i);
            $this->s($ws, "{$col}13", $h, self::NAVY, self::WHITE, 10, true);
            $this->border($ws, "{$col}13");
        }

        // Data rows — absen dulu, lalu hadir
        $sorted  = $members->sortBy(fn($m) => [$this->isAbsen($m, $records) ? 0 : 1, $m->nama]);
        $dataRow = 14;
        $idx     = 1;

        foreach ($sorted as $member) {
            $isAbsen = $this->isAbsen($member, $records);
            $record  = $records[$member->id] ?? null;
            $alasan  = $record?->alasan ?? null;

            $rowBg = $isAbsen
                ? self::RED_SOFT
                : ($idx % 2 === 0 ? self::GREY_LIGHT : self::WHITE);

            $ws->getRowDimension($dataRow)->setRowHeight(21.75);

            $cols = [
                ['A', $idx,                   'center', self::BLACK,   false, $rowBg],
                ['B', $member->nama,           'left',   self::BLACK,   false, $rowBg],
                ['C', $member->nik ?? '-',     'left',   self::BLACK,   false, $rowBg],
                ['D', $member->jabatan ?? '-', 'left',   self::BLACK,   false, $rowBg],
                ['E', $meta['shift'],          'center', self::BLACK,   false, $rowBg],
                ['F', $meta['factory'],        'left',   self::BLACK,   false, $rowBg],
                ['G', $member->mesin ?? '-',   'left',   self::BLACK,   false, $rowBg],
                ['H', $isAbsen ? 'Absen' : 'Hadir', 'center',
                    $isAbsen ? self::RED_DARK : self::GREEN_DARK, true,
                    $isAbsen ? self::RED_SOFT : self::GREEN_LIGHT],
                ['I', $alasan ?? '-',         'center', self::BLACK,   false, $rowBg],
                ['J', null,                   'left',   self::BLACK,   false, $rowBg],
            ];

            foreach ($cols as [$col, $val, $hAlign, $fontColor, $bold, $bg]) {
                $ws->getCell("{$col}{$dataRow}")->setValue($val);
                $ws->getStyle("{$col}{$dataRow}")->applyFromArray([
                    'font'      => ['name' => 'Arial', 'bold' => $bold, 'size' => 10, 'color' => ['argb' => $fontColor]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                    'alignment' => ['horizontal' => $hAlign, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFDDDDDD']]],
                ]);
            }

            $dataRow++;
            $idx++;
        }

        // Footer
        $ws->mergeCells("A{$dataRow}:J{$dataRow}");
        $this->s($ws, "A{$dataRow}",
            "Generated by HENKATEN BOARD  |  " . now()->format('d/m/Y H:i'),
            self::GREY_LIGHT, self::MID_GREY, 9, false);
        $ws->getRowDimension($dataRow)->setRowHeight(18);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  SHEET 2 — PROBLEM LOG 3M
    // ═══════════════════════════════════════════════════════════════════════
    private function buildProblemLog(
        Spreadsheet $wb,
        array $meta,
        Collection $logs,
    ): void {
        $ws = $wb->createSheet(1);
        $ws->setTitle('Problem Log 3M');
        $ws->setShowGridlines(false);

        // Column widths
        $ws->getColumnDimension('A')->setWidth(5);
        $ws->getColumnDimension('B')->setWidth(12);
        $ws->getColumnDimension('C')->setWidth(20);
        $ws->getColumnDimension('D')->setWidth(13);
        $ws->getColumnDimension('E')->setWidth(13);
        $ws->getColumnDimension('F')->setWidth(12);
        $ws->getColumnDimension('G')->setWidth(35);
        $ws->getColumnDimension('H')->setWidth(12);
        $ws->getColumnDimension('I')->setWidth(25);
        $ws->getColumnDimension('J')->setWidth(25);
        $ws->getColumnDimension('K')->setWidth(16);
        $ws->getColumnDimension('L')->setWidth(18);

        foreach ([1=>42, 2=>21.75, 3=>9.75, 4=>15.75, 5=>27.75, 6=>9.75,
                  7=>18, 8=>13.5, 9=>21.75, 10=>27.75] as $r => $h) {
            $ws->getRowDimension($r)->setRowHeight($h);
        }

        // ROW 1 & 2: Header
        $ws->mergeCells('A1:L1');
        $this->s($ws, 'A1', 'HENKATEN BOARD', self::NAVY, self::WHITE, 20, true);

        $ws->mergeCells('A2:L2');
        $this->s($ws, 'A2',
            "PROBLEM LOG 3M  |  {$meta['factory']}  |  Shift {$meta['shift']}  |  {$meta['tanggalFormatted']}",
            self::NAVY_MID, 'FFCCCCCC', 11, false);

        $ws->mergeCells('A3:L3');
        $this->s($ws, 'A3', null, self::NAVY_LIGHT, self::WHITE, 9, false);

        // Stat cards
        $totalLogs    = $logs->count();
        $openCount    = $logs->where('status', 'open')->count();
        $closedCount  = $logs->where('status', 'closed')->count();
        $machineCount = $logs->where('jenis', 'Machine')->count();
        $matCount     = $logs->where('jenis', 'Material')->count();
        $methodCount  = $logs->where('jenis', 'Method')->count();

        $logCards = [
            ['A', 'B', $totalLogs,    'TOTAL LOG',   self::NAVY_LIGHT,  self::NAVY],
            ['C', 'D', $openCount,    '⚠ OPEN',      self::RED_SOFT,    self::RED_DARK],
            ['E', 'F', $closedCount,  '✅ CLOSED',   self::GREEN_LIGHT, self::GREEN_DARK],
            ['G', 'H', $machineCount, '⚙ MACHINE',   self::NAVY_LIGHT,  self::NAVY],
            ['I', 'J', $matCount,     '📦 MATERIAL', self::ORANGE_SOFT, self::ORANGE],
            ['K', 'L', $methodCount,  '📋 METHOD',   self::GREEN_LIGHT, self::GREEN_DARK],
        ];

        foreach ($logCards as [$c1, $c2, $val, $label, $bg, $accent]) {
            $ws->mergeCells("{$c1}4:{$c2}6");
            $this->s($ws, "{$c1}4", $val, $bg, $accent, 20, true);
            $ws->mergeCells("{$c1}7:{$c2}8");
            $this->s($ws, "{$c1}7", $label, $bg, self::MID_GREY, 9, true);
        }

        // ROW 9: Sub-header tabel
        $ws->mergeCells('A9:L9');
        $this->s($ws, 'A9',
            "Data Problem Log: {$meta['factory']} | Shift {$meta['shift']} | Tanggal: {$meta['tanggalFormatted']}",
            self::DARK_GREY, self::WHITE, 10, true, 'left');

        // ROW 10: Column headers
        $headers = ['No', 'Jenis', 'Lokasi/Mesin', 'Waktu Mulai', 'Waktu Selesai',
                    'Durasi', 'Deskripsi Masalah', 'Status', 'Root Cause', 'Countermeasure', 'PIC', 'Catatan'];
        foreach ($headers as $i => $h) {
            $col = chr(ord('A') + $i);
            $this->s($ws, "{$col}10", $h, self::NAVY, self::WHITE, 10, true);
            $this->border($ws, "{$col}10");
        }

        // Data rows
        $jenisColors = [
            'Machine'  => [self::NAVY_LIGHT,  self::NAVY],
            'Material' => [self::ORANGE_SOFT, self::ORANGE],
            'Method'   => [self::GREEN_LIGHT, self::GREEN_DARK],
        ];

        $dataRow = 11;
        $idx     = 1;

        if ($logs->isEmpty()) {
            $ws->mergeCells("A{$dataRow}:L{$dataRow}");
            $this->s($ws, "A{$dataRow}", '✅  Tidak ada problem log hari ini.',
                self::GREEN_LIGHT, self::GREEN_DARK, 11, true);
            $ws->getRowDimension($dataRow)->setRowHeight(30);
            $dataRow++;
        } else {
            foreach ($logs as $log) {
                $isOpen = $log->status === 'open';
                [$jenisBg, $jenisColor] = $jenisColors[$log->jenis] ?? [self::NAVY_LIGHT, self::NAVY];
                $rowBg = $isOpen
                    ? ($idx % 2 === 0 ? 'FFFFCDD2' : self::RED_SOFT)
                    : ($idx % 2 === 0 ? self::GREY_LIGHT : self::WHITE);

                $ws->getRowDimension($dataRow)->setRowHeight(27.75);

                $cols = [
                    ['A', $idx,                                                  'center', self::BLACK,   false, $rowBg],
                    ['B', $log->jenis,                                           'center', $jenisColor,   true,  $jenisBg],
                    ['C', $log->lokasi,                                          'left',   self::BLACK,   false, $rowBg],
                    ['D', substr($log->waktu_mulai ?? '', 0, 5),                 'center', self::BLACK,   false, $rowBg],
                    ['E', $log->waktu_selesai ? substr($log->waktu_selesai,0,5) : '—', 'center', self::BLACK, false, $rowBg],
                    ['F', $log->durasi ?? ($isOpen ? 'ON GOING' : '—'),         'center', $isOpen ? self::RED_DARK : self::BLACK, $isOpen, $rowBg],
                    ['G', $log->deskripsi,                                       'left',   self::BLACK,   false, $rowBg],
                    ['H', $isOpen ? 'OPEN' : 'CLOSED',                          'center', $isOpen ? self::RED_DARK : self::GREEN_DARK, true, $isOpen ? self::RED_SOFT : self::GREEN_LIGHT],
                    ['I', $log->cause ?? '—',                                   'left',   self::BLACK,   false, $rowBg],
                    ['J', $log->countermeasure ?? '—',                          'left',   self::BLACK,   false, $rowBg],
                    ['K', $log->pic ?? '—',                                     'left',   self::BLACK,   false, $rowBg],
                    ['L', null,                                                  'left',   self::BLACK,   false, $rowBg],
                ];

                foreach ($cols as [$col, $val, $hAlign, $fontColor, $bold, $bg]) {
                    $ws->getCell("{$col}{$dataRow}")->setValue($val);
                    $ws->getStyle("{$col}{$dataRow}")->applyFromArray([
                        'font'      => ['name' => 'Arial', 'bold' => $bold, 'size' => 10, 'color' => ['argb' => $fontColor]],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                        'alignment' => ['horizontal' => $hAlign, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFDDDDDD']]],
                    ]);
                }

                $dataRow++;
                $idx++;
            }
        }

        // Footer
        $ws->mergeCells("A{$dataRow}:L{$dataRow}");
        $this->s($ws, "A{$dataRow}",
            "Generated by HENKATEN BOARD  |  " . now()->format('d/m/Y H:i'),
            self::GREY_LIGHT, self::MID_GREY, 9, false);
        $ws->getRowDimension($dataRow)->setRowHeight(18);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  SHEET 3 — DASHBOARD
    // ═══════════════════════════════════════════════════════════════════════
    private function buildDashboard(
        Spreadsheet $wb,
        array $meta,
        Collection $members,
        Collection $records,
        Collection $logs,
    ): void {
        $ws = $wb->createSheet(2);
        $ws->setTitle('Dashboard');
        $ws->setShowGridlines(false);

        foreach (range('A', 'H') as $col) {
            $ws->getColumnDimension($col)->setWidth(16);
        }

        foreach ([1=>42, 2=>21.75, 3=>9.75, 4=>24, 5=>15.75, 6=>27.75, 7=>9.75,
                  8=>18, 9=>13.5, 10=>7.5, 11=>24, 12=>15.75, 13=>27.75, 14=>9.75,
                  15=>18, 16=>13.5, 17=>7.5, 18=>24, 19=>15.75, 20=>27.75, 21=>9.75,
                  22=>18, 23=>13.5, 25=>18] as $r => $h) {
            $ws->getRowDimension($r)->setRowHeight($h);
        }

        // Hitung semua data
        $totalMembers = $members->count();
        $absenCount   = $members->filter(fn($m) => $this->isAbsen($m, $records))->count();
        $hadirCount   = $totalMembers - $absenCount;
        $pctHadir     = $totalMembers ? round($hadirCount / $totalMembers * 100, 1) . '%' : '-%';
        $totalLogs    = $logs->count();
        $openCount    = $logs->where('status', 'open')->count();
        $closedCount  = $logs->where('status', 'closed')->count();
        $machineCount = $logs->where('jenis', 'Machine')->count();
        $matCount     = $logs->where('jenis', 'Material')->count();
        $methodCount  = $logs->where('jenis', 'Method')->count();

        // ROW 1 & 2: Header
        $ws->mergeCells('A1:H1');
        $this->s($ws, 'A1', 'HENKATEN BOARD — LAPORAN HARIAN', self::NAVY, self::WHITE, 20, true);

        $ws->mergeCells('A2:H2');
        $this->s($ws, 'A2',
            "{$meta['factory']}  |  Shift {$meta['shift']}  |  {$meta['tanggalFormatted']}",
            self::NAVY_MID, 'FFCCCCCC', 11, false);

        $ws->mergeCells('A3:H3');
        $this->s($ws, 'A3', null, self::NAVY_LIGHT, self::WHITE, 9, false);

        // ── SECTION: 4M SUMMARY ───────────────────────────────────────────
        $ws->mergeCells('A4:H4');
        $this->s($ws, 'A4', '4M SUMMARY', self::DARK_GREY, self::WHITE, 12, true);

        $cards4M = [
            ['A', 'B', $absenCount,   '👤 MAN (ABSEN)', self::RED_SOFT,    self::RED_DARK],
            ['C', 'D', $machineCount, '⚙ MACHINE',      self::NAVY_LIGHT,  self::NAVY],
            ['E', 'F', $matCount,     '📦 MATERIAL',    self::ORANGE_SOFT, self::ORANGE],
            ['G', 'H', $methodCount,  '📋 METHOD',      self::GREEN_LIGHT, self::GREEN_DARK],
        ];
        foreach ($cards4M as [$c1, $c2, $val, $label, $bg, $accent]) {
            $ws->mergeCells("{$c1}5:{$c2}7");
            $this->s($ws, "{$c1}5", $val, $bg, $accent, 22, true);
            $ws->mergeCells("{$c1}8:{$c2}9");
            $this->s($ws, "{$c1}8", $label, $bg, self::MID_GREY, 9, true);
        }

        // ── SECTION: KEHADIRAN ────────────────────────────────────────────
        $ws->mergeCells('A11:H11');
        $this->s($ws, 'A11', 'KEHADIRAN', self::DARK_GREY, self::WHITE, 12, true);

        $kehadiranCards = [
            ['A', 'B', $totalMembers, 'TOTAL',   self::NAVY_LIGHT,  self::NAVY],
            ['C', 'D', $hadirCount,   'HADIR',   self::GREEN_LIGHT, self::GREEN_DARK],
            ['E', 'F', $absenCount,   'ABSEN',   self::RED_SOFT,    self::RED_DARK],
            ['G', 'H', $pctHadir,    '% HADIR', self::ORANGE_SOFT, self::ORANGE],
        ];
        foreach ($kehadiranCards as [$c1, $c2, $val, $label, $bg, $accent]) {
            $ws->mergeCells("{$c1}12:{$c2}14");
            $this->s($ws, "{$c1}12", $val, $bg, $accent, 22, true);
            $ws->mergeCells("{$c1}15:{$c2}16");
            $this->s($ws, "{$c1}15", $label, $bg, self::MID_GREY, 9, true);
        }

        // ── SECTION: PROBLEM LOG ──────────────────────────────────────────
        $ws->mergeCells('A18:H18');
        $this->s($ws, 'A18', 'PROBLEM LOG', self::DARK_GREY, self::WHITE, 12, true);

        $logCards = [
            ['A', 'B', $totalLogs,   'TOTAL LOG', self::NAVY_LIGHT,  self::NAVY],
            ['C', 'D', $openCount,   'OPEN',      self::RED_SOFT,    self::RED_DARK],
            ['E', 'F', $closedCount, 'CLOSED',    self::GREEN_LIGHT, self::GREEN_DARK],
            ['G', 'H', $machineCount,'MACHINE',   self::NAVY_LIGHT,  self::NAVY],
        ];
        foreach ($logCards as [$c1, $c2, $val, $label, $bg, $accent]) {
            $ws->mergeCells("{$c1}19:{$c2}21");
            $this->s($ws, "{$c1}19", $val, $bg, $accent, 22, true);
            $ws->mergeCells("{$c1}22:{$c2}23");
            $this->s($ws, "{$c1}22", $label, $bg, self::MID_GREY, 9, true);
        }

        // Footer
        $ws->mergeCells('A25:H25');
        $this->s($ws, 'A25',
            "Generated by HENKATEN BOARD  |  " . now()->format('d/m/Y H:i'),
            self::GREY_LIGHT, self::MID_GREY, 9, false);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  HELPERS
    // ═══════════════════════════════════════════════════════════════════════

    private function s(
        Worksheet $ws,
        string    $coord,
        mixed     $value,
        string    $bgArgb,
        string    $fontArgb,
        int       $fontSize = 10,
        bool      $bold     = false,
        string    $hAlign   = 'center',
        bool      $wrap     = true,
    ): void {
        $ws->getCell($coord)->setValue($value);
        $ws->getStyle($coord)->applyFromArray([
            'font' => [
                'name'  => 'Arial',
                'bold'  => $bold,
                'size'  => $fontSize,
                'color' => ['argb' => $fontArgb],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => $bgArgb],
            ],
            'alignment' => [
                'horizontal' => $hAlign,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => $wrap,
            ],
        ]);
    }

    private function border(Worksheet $ws, string $range): void
    {
        $ws->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFDDDDDD'],
                ],
            ],
        ]);
    }

    private function isAbsen($member, Collection $records): bool
    {
        $rec = $records[$member->id] ?? null;
        return $rec && $rec->status === 'absen';
    }

    private function countAlasan(Collection $members, Collection $records, string $alasan): int
    {
        return $members->filter(function ($m) use ($records, $alasan) {
            $rec = $records[$m->id] ?? null;
            return $rec && $rec->status === 'absen'
                && strcasecmp($rec->alasan ?? '', $alasan) === 0;
        })->count();
    }
}