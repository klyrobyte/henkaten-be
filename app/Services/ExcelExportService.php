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
    const NAVY = 'FF1F3C88';
    const NAVY_MID = 'FF2C4A9E';
    const NAVY_LIGHT = 'FFEEF1FA';
    const GREEN_DARK = 'FF2E7D32';
    const GREEN_LIGHT = 'FFE8F5E9';
    const RED_DARK = 'FFC62828';
    const RED_SOFT = 'FFFFEBEE';
    const ORANGE = 'FFE65100';
    const ORANGE_SOFT = 'FFFFF3E0';
    const BLUE_DARK = 'FF1565C0';
    const BLUE_SOFT = 'FFE3F2FD';
    const PURPLE_DARK = 'FF7B1FA2';
    const PURPLE_SOFT = 'FFF3E5F5';
    const DARK_GREY = 'FF37474F';
    const MID_GREY = 'FF757575';
    const WHITE = 'FFFFFFFF';
    const GREY_LIGHT = 'FFF5F5F5';
    const BLACK = 'FF000000';

    // ═══════════════════════════════════════════════════════════════════════
    //  ENTRY POINT
    // ═══════════════════════════════════════════════════════════════════════
    public function buildFullReport(
        string $factory,
        string $shift,
        string $tanggal,
        Collection $members,
        Collection $records,        // AbsenceRecord keyed by member_id
        Collection $logs,           // ProblemLog
        Collection $replacements,
        ?string $tanggalFormatted = null
    ): string {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $tanggalFormatted = $tanggalFormatted ?? Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM YYYY');
        $meta = compact('factory', 'shift', 'tanggal', 'tanggalFormatted');

        $this->buildRekapAbsen($spreadsheet, $meta, $members, $records, $replacements, $logs);
        $this->buildProblemLog($spreadsheet, $meta, $logs);
        $this->buildDashboard($spreadsheet, $meta, $members, $records, $logs);

        // Aktifkan sheet Dashboard saat dibuka
        $spreadsheet->setActiveSheetIndex(2);

        $dir = storage_path('app/temp');
        if (!is_dir($dir))
            mkdir($dir, 0755, true);

        $path = $dir . "/report_{$factory}_Shift{$shift}_{$tanggal}_" . uniqid() . ".xlsx";
        (new Xlsx($spreadsheet))->save($path);

        return $path;
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  SHEET 1  - REKAP ABSEN
    // ═══════════════════════════════════════════════════════════════════════
    private function buildRekapAbsen(
        Spreadsheet $wb,
        array $meta,
        Collection $members,
        Collection $records,
        Collection $replacements,
        Collection $logs
    ): void {
        $ws = $wb->createSheet(0);
        $ws->setTitle('Rekap Absen');
        $ws->setShowGridlines(false);

        // Column widths
        $ws->getColumnDimension('A')->setWidth(5);
        $ws->getColumnDimension('B')->setWidth(15);
        $ws->getColumnDimension('C')->setWidth(28);
        $ws->getColumnDimension('D')->setWidth(14);
        $ws->getColumnDimension('E')->setWidth(22);
        $ws->getColumnDimension('F')->setWidth(8);
        $ws->getColumnDimension('G')->setWidth(16);
        $ws->getColumnDimension('H')->setWidth(18);
        $ws->getColumnDimension('I')->setWidth(12);
        $ws->getColumnDimension('J')->setWidth(14);
        $ws->getColumnDimension('K')->setWidth(25);
        $ws->getColumnDimension('L')->setWidth(20);

        // Row heights
        foreach ([
            1 => 42,
            2 => 21.75,
            3 => 9.75,
            4 => 15.75,
            5 => 27.75,
            6 => 9.75,
            7 => 18,
            8 => 13.5,
            9 => 7.5,
            10 => 19.5,
            11 => 6,
            12 => 21.75,
            13 => 27.75
        ] as $r => $h) {
            $ws->getRowDimension($r)->setRowHeight($h);
        }

        // ROW 1: Header utama
        $ws->mergeCells('A1:L1');
        $this->s($ws, 'A1', 'HENKATEN BOARD', self::NAVY, self::WHITE, 20, true);

        // ROW 2: Subtitle
        $ws->mergeCells('A2:L2');
        $this->s(
            $ws,
            'A2',
            "REKAP ABSENSI  |  {$meta['factory']}  |  Shift {$meta['shift']}  |  {$meta['tanggalFormatted']}",
            self::NAVY_MID,
            'FFCCCCCC',
            11,
            false
        );

        // ROW 3: spacer
        $ws->mergeCells('A3:L3');
        $this->s($ws, 'A3', null, self::NAVY_LIGHT, self::WHITE, 9, false);

        // Hitung stats
        $totalMembers = $members->count();
        $absenCount = $members->filter(fn($m) => $this->isAbsen($m, $records))->count();
        $hadirCount = $totalMembers - $absenCount;
        $pctHadir = $totalMembers ? round($hadirCount / $totalMembers * 100, 1) . '%' : '-%';
        $cutiCount = $this->countAlasan($members, $records, 'Cuti');
        $sakitCount = $this->countAlasan($members, $records, 'Sakit');
        $ijinCount = $this->countAlasan($members, $records, 'Ijin') + $this->countAlasan($members, $records, 'Izin');

        // ROWS 4-8: Stat cards (tanpa Cuti)
        $statCards = [
            ['A', 'C', $totalMembers, 'TOTAL ANGGOTA', self::NAVY_LIGHT, self::NAVY],
            ['D', 'F', $hadirCount, '✓ HADIR', self::GREEN_LIGHT, self::GREEN_DARK],
            ['G', 'I', $absenCount, '✗ TIDAK HADIR', self::RED_SOFT, self::RED_DARK],
            ['J', 'L', $pctHadir, '% KEHADIRAN', self::ORANGE_SOFT, self::ORANGE],
        ];
        foreach ($statCards as [$c1, $c2, $val, $label, $bg, $accent]) {
            $ws->mergeCells("{$c1}4:{$c2}6");
            $this->s($ws, "{$c1}4", $val, $bg, $accent, 20, true);
            $ws->mergeCells("{$c1}7:{$c2}8");
            $this->s($ws, "{$c1}7", $label, $bg, self::MID_GREY, 9, true);
        }

        // ROW 10: Alasan breakdown (without Alpha)
        $alasanCards = [
            ['A', 'D', "{$cutiCount}  Cuti", self::BLUE_SOFT, self::BLUE_DARK],
            ['E', 'H', "{$sakitCount}  Sakit", self::ORANGE_SOFT, self::ORANGE],
            ['I', 'L', "{$ijinCount}  Ijin", self::PURPLE_SOFT, self::PURPLE_DARK],
        ];
        foreach ($alasanCards as [$c1, $c2, $val, $bg, $accent]) {
            $ws->mergeCells("{$c1}10:{$c2}10");
            $this->s($ws, "{$c1}10", $val, $bg, $accent, 9, true);
        }

        // ROW 12: Sub-header tabel
        $ws->mergeCells('A12:L12');
        $this->s(
            $ws,
            'A12',
            "Data Absensi: {$meta['factory']} | Shift {$meta['shift']} | Tanggal: {$meta['tanggalFormatted']}",
            self::DARK_GREY,
            self::WHITE,
            10,
            true,
            'left'
        );

        // ROW 13: Column headers
        $headers = ['No', 'Tanggal', 'Nama Lengkap', 'NIK', 'Jabatan', 'Shift', 'Factory', 'Mesin', 'Status', 'Alasan', 'Pengganti', 'Catatan'];
        foreach ($headers as $i => $h) {
            $col = chr(ord('A') + $i);
            $this->s($ws, "{$col}13", $h, self::NAVY, self::WHITE, 10, true);
            $this->border($ws, "{$col}13");
        }

        // Data rows  - absen dulu, lalu hadir
        $sorted = $members->sortBy(fn($m) => [$this->isAbsen($m, $records) ? 0 : 1, $m->nama]);
        $dataRow = 14;
        $idx = 1;

        $penggantiByMesin = [];
        foreach ($replacements as $repl) {
            $srcMesin = $repl->source_machine ?? null;
            if ($srcMesin) {
                $penggantiByMesin[$srcMesin][] = $repl->member?->nama ?? '-';
            }
        }

        foreach ($sorted as $member) {
            $isAbsen = $this->isAbsen($member, $records);
            $mesinLower = strtolower($member->mesin ?? '');
            $isKeyPerson = str_starts_with($mesinLower, 'gl')
                || str_starts_with($mesinLower, 'tl')
                || str_starts_with($mesinLower, 'ky');
            $jabatanDisplay = $isKeyPerson ? 'Pengawas' : ($member->jabatan ?? '-');

            $record = $records[$member->id] ?? null;
            $alasan = $record?->reason ?? null;  // gunakan 'reason', bukan 'alasan'

            $rowBg = $isAbsen
                ? self::RED_SOFT
                : ($idx % 2 === 0 ? self::GREY_LIGHT : self::WHITE);

            $ws->getRowDimension($dataRow)->setRowHeight(21.75);

            $cols = [
                ['A', $idx, 'center', self::BLACK, false, $rowBg],
                ['B', $meta['tanggal'], 'center', self::BLACK, false, $rowBg],
                ['C', $member->nama, 'left', self::BLACK, false, $rowBg],
                ['D', $member->nik ?? '-', 'left', self::BLACK, false, $rowBg],
                ['E', $jabatanDisplay, 'left', self::BLACK, false, $rowBg],
                ['F', $meta['shift'], 'center', self::BLACK, false, $rowBg],
                ['G', $meta['factory'], 'left', self::BLACK, false, $rowBg],
                ['H', $member->mesin ?? '-', 'left', self::BLACK, false, $rowBg],
                [
                    'I',
                    $isAbsen ? 'Absen' : 'Hadir',
                    'center',
                    $isAbsen ? self::RED_DARK : self::GREEN_DARK,
                    true,
                    $isAbsen ? self::RED_SOFT : self::GREEN_LIGHT
                ],
                ['J', $isAbsen ? ($alasan ?? '-') : '-', 'center', self::BLACK, false, $rowBg],
                ['K', $isAbsen && !empty($penggantiByMesin[$member->mesin ?? '']) ? 'Digantikan oleh ' . implode(' & ', $penggantiByMesin[$member->mesin]) : '-', 'center', self::BLACK, false, $rowBg],
                ['L', null, 'left', self::BLACK, false, $rowBg],
            ];

            foreach ($cols as [$col, $val, $hAlign, $fontColor, $bold, $bg]) {
                $ws->getCell("{$col}{$dataRow}")->setValue($val);
                $ws->getStyle("{$col}{$dataRow}")->applyFromArray([
                    'font' => ['name' => 'Arial', 'bold' => $bold, 'size' => 10, 'color' => ['argb' => $fontColor]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                    'alignment' => ['horizontal' => $hAlign, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFDDDDDD']]],
                ]);
            }

            $dataRow++;
            $idx++;
        }

        // Footer
        $ws->mergeCells("A{$dataRow}:L{$dataRow}");
        $this->s(
            $ws,
            "A{$dataRow}",
            "Generated by HENKATEN BOARD  |  " . now()->format('d/m/Y H:i'),
            self::GREY_LIGHT,
            self::MID_GREY,
            9,
            false
        );
        $ws->getRowDimension($dataRow)->setRowHeight(18);

        // ── Task 2: SUMMARY TABLE BLOCK ──
        $dataRow += 2; // Blank row separator

        $kyAbsenCount = $members->filter(function ($m) use ($records) {
            return $this->isAbsen($m, $records) && str_starts_with(strtolower($m->mesin ?? ''), 'ky');
        })->count();
        $kyTotalCount = $members->filter(function ($m) {
            return str_starts_with(strtolower($m->mesin ?? ''), 'ky');
        })->count();
        $activeMC = $logs->where('status', 'open')->count();

        $statusLabels = ['Normal', 'Ringan', 'Khusus', 'Bahaya'];
        $statusIndex = 0;
        if ($absenCount > $kyTotalCount) {
            if ($activeMC === 0)
                $statusIndex = 1;
            elseif ($activeMC === 1)
                $statusIndex = 2;
            elseif ($activeMC >= 2)
                $statusIndex = 3;
        } else {
            if ($activeMC === 0)
                $statusIndex = 0;
            elseif ($activeMC === 1)
                $statusIndex = 2;
            elseif ($activeMC >= 2)
                $statusIndex = 3;
        }
        $statusLabel = $statusLabels[$statusIndex] ?? 'Normal';

        $summaryData = [
            ['RINGKASAN KEHADIRAN'],
            ['Tanggal Export:', now()->format('d/m/Y H:i')],
            ['Periode:', $meta['tanggalFormatted']],
            ['Total Hadir:', $hadirCount],
            ['Total Absen:', $absenCount],
            ['Total MC:', $activeMC],
            ['Total KY Absen:', $kyAbsenCount],
            ['Status Keseluruhan:', strtoupper($statusLabel)],
        ];

        foreach ($summaryData as $i => $row) {
            $ws->setCellValue("B{$dataRow}", $row[0]);
            $ws->getStyle("B{$dataRow}")->getFont()->setBold(true);
            if (isset($row[1])) {
                $ws->setCellValue("C{$dataRow}", $row[1]);
                $ws->getStyle("C{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            }
            $ws->getRowDimension($dataRow)->setRowHeight(18);
            $dataRow++;
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  SHEET 2  - PROBLEM LOG 3M
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
        $ws->getColumnDimension('C')->setWidth(12);
        $ws->getColumnDimension('D')->setWidth(20);
        $ws->getColumnDimension('E')->setWidth(13);
        $ws->getColumnDimension('F')->setWidth(13);
        $ws->getColumnDimension('G')->setWidth(12);
        $ws->getColumnDimension('H')->setWidth(35);
        $ws->getColumnDimension('I')->setWidth(12);
        $ws->getColumnDimension('J')->setWidth(25);
        $ws->getColumnDimension('K')->setWidth(25);
        $ws->getColumnDimension('L')->setWidth(16);
        $ws->getColumnDimension('M')->setWidth(18);

        foreach ([
            1 => 42,
            2 => 21.75,
            3 => 9.75,
            4 => 15.75,
            5 => 27.75,
            6 => 9.75,
            7 => 18,
            8 => 13.5,
            9 => 21.75,
            10 => 27.75
        ] as $r => $h) {
            $ws->getRowDimension($r)->setRowHeight($h);
        }

        // ROW 1 & 2: Header
        $ws->mergeCells('A1:M1');
        $this->s($ws, 'A1', 'HENKATEN BOARD', self::NAVY, self::WHITE, 20, true);

        $ws->mergeCells('A2:M2');
        $this->s(
            $ws,
            'A2',
            "PROBLEM LOG 3M  |  {$meta['factory']}  |  Shift {$meta['shift']}  |  {$meta['tanggalFormatted']}",
            self::NAVY_MID,
            'FFCCCCCC',
            11,
            false
        );

        $ws->mergeCells('A3:M3');
        $this->s($ws, 'A3', null, self::NAVY_LIGHT, self::WHITE, 9, false);

        // Stat cards
        $totalLogs = $logs->count();
        $openCount = $logs->where('status', 'open')->count();
        $closedCount = $logs->where('status', 'closed')->count();
        $jenisList = $this->getDynamicJenis();

        $baseCards = [
            [$totalLogs, 'TOTAL LOG', self::NAVY_LIGHT, self::NAVY],
            [$openCount, '⚠ OPEN', self::RED_SOFT, self::RED_DARK],
            [$closedCount, '✅ CLOSED', self::GREEN_LIGHT, self::GREEN_DARK],
        ];

        $colors = [
            [self::NAVY_LIGHT, self::NAVY, '⚙ '],
            [self::ORANGE_SOFT, self::ORANGE, '📦 '],
            [self::GREEN_LIGHT, self::GREEN_DARK, '📋 '],
            ['FFEDE7F6', 'FF5E35B1', '⚡ '], // purple
            ['FFE3F2FD', 'FF1565C0', '💧 '], // blue
        ];

        foreach ($jenisList as $idx => $jenis) {
            $count = $logs->where('jenis', $jenis)->count();
            $c = $colors[$idx % count($colors)];
            $baseCards[] = [$count, $c[2] . strtoupper($jenis), $c[0], $c[1]];
        }

        $logCards = [];
        $totalCards = count($baseCards);
        $colsPerCard = floor(12 / ($totalCards ?: 1));
        if ($colsPerCard < 1)
            $colsPerCard = 1;

        $colLetter = 'A';
        foreach ($baseCards as $i => $card) {
            if ($i == $totalCards - 1) {
                $endLetter = 'M';
            } else {
                $endLetter = chr(ord($colLetter) + $colsPerCard - 1);
                if ($endLetter > 'M')
                    $endLetter = 'M';
            }
            $logCards[] = [$colLetter, $endLetter, $card[0], $card[1], $card[2], $card[3]];
            $colLetter = chr(ord($endLetter) + 1);
            if ($colLetter > 'M')
                break; // Failsafe
        }

        foreach ($logCards as [$c1, $c2, $val, $label, $bg, $accent]) {
            $ws->mergeCells("{$c1}4:{$c2}6");
            $this->s($ws, "{$c1}4", $val, $bg, $accent, 20, true);
            $ws->mergeCells("{$c1}7:{$c2}8");
            $this->s($ws, "{$c1}7", $label, $bg, self::MID_GREY, 9, true);
        }

        // ROW 9: Sub-header tabel
        $ws->mergeCells('A9:M9');
        $this->s(
            $ws,
            'A9',
            "Data Problem Log: {$meta['factory']} | Shift {$meta['shift']} | Tanggal: {$meta['tanggalFormatted']}",
            self::DARK_GREY,
            self::WHITE,
            10,
            true,
            'left'
        );

        // ROW 10: Column headers
        $headers = [
            'No',
            'Jenis',
            'Tanggal',
            'Lokasi/Mesin',
            'Waktu Mulai',
            'Waktu Selesai',
            'Durasi',
            'Deskripsi Masalah',
            'Status',
            'Root Cause',
            'Countermeasure',
            'PIC',
            'Catatan'
        ];
        foreach ($headers as $i => $h) {
            $col = chr(ord('A') + $i);
            $this->s($ws, "{$col}10", $h, self::NAVY, self::WHITE, 10, true);
            $this->border($ws, "{$col}10");
        }

        // Data rows
        $jenisColors = [];
        foreach ($jenisList as $idx => $jenis) {
            $c = $colors[$idx % count($colors)];
            $jenisColors[$jenis] = [$c[0], $c[1]];
        }

        $dataRow = 11;
        $idx = 1;

        if ($logs->isEmpty()) {
            $ws->mergeCells("A{$dataRow}:M{$dataRow}");
            $this->s(
                $ws,
                "A{$dataRow}",
                '✅  Tidak ada problem log hari ini.',
                self::GREEN_LIGHT,
                self::GREEN_DARK,
                11,
                true
            );
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

                $waktuSelesaiStr = ' -';
                if ($log->waktu_selesai) {
                    $timeStr = substr($log->waktu_selesai, 0, 5);
                    $waktuSelesaiStr = $timeStr;
                    if ($log->updated_at) {
                        $tglSelesai = $log->updated_at->format('Y-m-d');
                        if ($log->tanggal !== $tglSelesai) {
                            $waktuSelesaiStr = $log->updated_at->format('d/m/y') . ' ' . $timeStr;
                        }
                    }
                }

                $cols = [
                    ['A', $idx, 'center', self::BLACK, false, $rowBg],
                    ['B', $log->jenis, 'center', $jenisColor, true, $jenisBg],
                    ['C', $log->tanggal ?? $meta['tanggal'], 'center', self::BLACK, false, $rowBg],
                    ['D', $log->lokasi, 'left', self::BLACK, false, $rowBg],
                    ['E', substr($log->waktu_mulai ?? '', 0, 5), 'center', self::BLACK, false, $rowBg],
                    ['F', $waktuSelesaiStr, 'center', self::BLACK, false, $rowBg],
                    ['G', $log->durasi ?? ($isOpen ? 'ON GOING' : ' -'), 'center', $isOpen ? self::RED_DARK : self::BLACK, $isOpen, $rowBg],
                    ['H', $log->deskripsi, 'left', self::BLACK, false, $rowBg],
                    ['I', $isOpen ? 'OPEN' : 'CLOSED', 'center', $isOpen ? self::RED_DARK : self::GREEN_DARK, true, $isOpen ? self::RED_SOFT : self::GREEN_LIGHT],
                    ['J', $log->cause ?? ' -', 'left', self::BLACK, false, $rowBg],
                    ['K', $log->countermeasure ?? ' -', 'left', self::BLACK, false, $rowBg],
                    ['L', $log->pic ?? ' -', 'left', self::BLACK, false, $rowBg],
                    ['M', null, 'left', self::BLACK, false, $rowBg],
                ];

                foreach ($cols as [$col, $val, $hAlign, $fontColor, $bold, $bg]) {
                    $ws->getCell("{$col}{$dataRow}")->setValue($val);
                    $ws->getStyle("{$col}{$dataRow}")->applyFromArray([
                        'font' => ['name' => 'Arial', 'bold' => $bold, 'size' => 10, 'color' => ['argb' => $fontColor]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                        'alignment' => ['horizontal' => $hAlign, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFDDDDDD']]],
                    ]);
                }

                $dataRow++;
                $idx++;
            }
        }

        // Footer
        $ws->mergeCells("A{$dataRow}:M{$dataRow}");
        $this->s(
            $ws,
            "A{$dataRow}",
            "Generated by HENKATEN BOARD  |  " . now()->format('d/m/Y H:i'),
            self::GREY_LIGHT,
            self::MID_GREY,
            9,
            false
        );
        $ws->getRowDimension($dataRow)->setRowHeight(18);
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  SHEET 3  - DASHBOARD
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

        foreach ([
            1 => 42,
            2 => 21.75,
            3 => 9.75,
            4 => 24,
            5 => 15.75,
            6 => 27.75,
            7 => 9.75,
            8 => 18,
            9 => 13.5,
            10 => 7.5,
            11 => 24,
            12 => 15.75,
            13 => 27.75,
            14 => 9.75,
            15 => 18,
            16 => 13.5,
            17 => 7.5,
            18 => 24,
            19 => 15.75,
            20 => 27.75,
            21 => 9.75,
            22 => 18,
            23 => 13.5,
            25 => 18
        ] as $r => $h) {
            $ws->getRowDimension($r)->setRowHeight($h);
        }

        // Hitung semua data
        $totalMembers = $members->count();
        $absenCount = $members->filter(fn($m) => $this->isAbsen($m, $records))->count();
        $hadirCount = $totalMembers - $absenCount;
        $pctHadir = $totalMembers ? round($hadirCount / $totalMembers * 100, 1) . '%' : '-%';
        $totalLogs = $logs->count();
        $openCount = $logs->where('status', 'open')->count();
        $closedCount = $logs->where('status', 'closed')->count();
        $jenisList = $this->getDynamicJenis();

        // ROW 1 & 2: Header
        $ws->mergeCells('A1:H1');
        $this->s($ws, 'A1', 'HENKATEN BOARD  - LAPORAN HARIAN', self::NAVY, self::WHITE, 20, true);

        $ws->mergeCells('A2:H2');
        $this->s(
            $ws,
            'A2',
            "{$meta['factory']}  |  Shift {$meta['shift']}  |  {$meta['tanggalFormatted']}",
            self::NAVY_MID,
            'FFCCCCCC',
            11,
            false
        );

        $ws->mergeCells('A3:H3');
        $this->s($ws, 'A3', null, self::NAVY_LIGHT, self::WHITE, 9, false);

        // ── SECTION: 4M SUMMARY ───────────────────────────────────────────
        $ws->mergeCells('A4:H4');
        $this->s($ws, 'A4', '4M SUMMARY', self::DARK_GREY, self::WHITE, 12, true);

        $baseCards = [
            [$absenCount, '👤 MAN (ABSEN)', self::RED_SOFT, self::RED_DARK]
        ];

        $colors = [
            [self::NAVY_LIGHT, self::NAVY, '⚙ '],
            [self::ORANGE_SOFT, self::ORANGE, '📦 '],
            [self::GREEN_LIGHT, self::GREEN_DARK, '📋 '],
            ['FFEDE7F6', 'FF5E35B1', '⚡ '],
            ['FFE3F2FD', 'FF1565C0', '💧 '],
        ];

        foreach ($jenisList as $idx => $jenis) {
            $count = $logs->where('jenis', $jenis)->count();
            $c = $colors[$idx % count($colors)];
            $baseCards[] = [$count, $c[2] . strtoupper($jenis), $c[0], $c[1]];
        }

        $cards4M = [];
        $totalCards = count($baseCards);
        $colsPerCard = floor(8 / ($totalCards ?: 1));
        if ($colsPerCard < 1)
            $colsPerCard = 1;

        $colLetter = 'A';
        foreach ($baseCards as $i => $card) {
            if ($i == $totalCards - 1) {
                $endLetter = 'H';
            } else {
                $endLetter = chr(ord($colLetter) + $colsPerCard - 1);
                if ($endLetter > 'H')
                    $endLetter = 'H';
            }
            $cards4M[] = [$colLetter, $endLetter, $card[0], $card[1], $card[2], $card[3]];
            $colLetter = chr(ord($endLetter) + 1);
            if ($colLetter > 'H')
                break;
        }

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
            ['A', 'B', $totalMembers, 'TOTAL', self::NAVY_LIGHT, self::NAVY],
            ['C', 'D', $hadirCount, 'HADIR', self::GREEN_LIGHT, self::GREEN_DARK],
            ['E', 'F', $absenCount, 'ABSEN', self::RED_SOFT, self::RED_DARK],
            ['G', 'H', $pctHadir, '% HADIR', self::ORANGE_SOFT, self::ORANGE],
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

        // Splitting 3 cards over 8 columns (A-C, D-E, F-H or similar)
        $logCards = [
            ['A', 'C', $totalLogs, 'TOTAL LOG', self::NAVY_LIGHT, self::NAVY],
            ['D', 'E', $openCount, 'OPEN', self::RED_SOFT, self::RED_DARK],
            ['F', 'H', $closedCount, 'CLOSED', self::GREEN_LIGHT, self::GREEN_DARK],
        ];
        foreach ($logCards as [$c1, $c2, $val, $label, $bg, $accent]) {
            $ws->mergeCells("{$c1}19:{$c2}21");
            $this->s($ws, "{$c1}19", $val, $bg, $accent, 22, true);
            $ws->mergeCells("{$c1}22:{$c2}23");
            $this->s($ws, "{$c1}22", $label, $bg, self::MID_GREY, 9, true);
        }

        // Footer
        $ws->mergeCells('A25:H25');
        $this->s(
            $ws,
            'A25',
            "Generated by HENKATEN BOARD  |  " . now()->format('d/m/Y H:i'),
            self::GREY_LIGHT,
            self::MID_GREY,
            9,
            false
        );
    }

    // ═══════════════════════════════════════════════════════════════════════
    //  HELPERS
    // ═══════════════════════════════════════════════════════════════════════

    private function s(
        Worksheet $ws,
        string $coord,
        mixed $value,
        string $bgArgb,
        string $fontArgb,
        int $fontSize = 10,
        bool $bold = false,
        string $hAlign = 'center',
        bool $wrap = true,
    ): void {
        $ws->getCell($coord)->setValue($value);
        $ws->getStyle($coord)->applyFromArray([
            'font' => [
                'name' => 'Arial',
                'bold' => $bold,
                'size' => $fontSize,
                'color' => ['argb' => $fontArgb],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => $bgArgb],
            ],
            'alignment' => [
                'horizontal' => $hAlign,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => $wrap,
            ],
        ]);
    }

    private function border(Worksheet $ws, string $range): void
    {
        $ws->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFDDDDDD'],
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
                && strcasecmp($rec->reason ?? '', $alasan) === 0;
        })->count();
    }

    private function getDynamicJenis(): array
    {
        try {
            $type = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM problem_logs WHERE Field = 'jenis'")[0]->Type;
            preg_match('/^enum\((.*)\)$/', $type, $matches);
            $types = [];
            foreach (explode(',', $matches[1]) as $value) {
                $types[] = trim($value, "'");
            }
            return $types;
        } catch (\Exception $e) {
            return ['Machine', 'Material', 'Method']; // fallback backward compatibility
        }
    }
}