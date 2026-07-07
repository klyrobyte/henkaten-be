<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use App\Models\Member;
use App\Models\MemberSkill;
use App\Models\Machine;
use App\Models\MachineProcess;
use App\Services\ScContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * SkillController — Manajemen Skill Member per Mesin
 *
 * Halaman ini memungkinkan admin/GL untuk mengatur persentase
 * kemampuan (skill) setiap member pada setiap mesin/pos yang
 * ada di factory mereka.
 *
 * Skill % ≥ 75 = berhak menjadi pengganti untuk mesin tersebut.
 */
class SkillController extends Controller
{
    // GET /admin/skills  — halaman utama
    public function index(Request $request)
    {
        $user    = Auth::user();
        $scId    = ScContext::id();
        $isSA    = $user->isSuperAdmin();
        $allowed = (array) $user->factory;

        // Factories yang bisa diakses
        $factoriesQ = Factory::where('sc_id', $scId)->orderBy('order_index');
        if (!$isSA) {
            $factoriesQ->whereIn('name', $allowed);
        }
        $factories = $factoriesQ->get();

        $factory = $request->get('factory', $factories->first()?->name ?? '');
        if (!$isSA && !in_array($factory, $allowed)) {
            $factory = $allowed[0] ?? '';
        }

        $shift = $request->get('shift', 'all');

        // Daftar mesin di factory ini
        $machines = Machine::where('sc_id', $scId)
            ->where('factory', $factory)
            ->orderBy('name')
            ->pluck('name')
            ->unique()
            ->values();

        // Daftar member
        $memberQ = Member::where('sc_id', $scId)
            ->where('factory', $factory)
            ->where('status', 'active')
            ->orderBy('nama');

        if ($shift !== 'all') {
            $memberQ->whereIn('shift', \App\Services\NonShiftResolver::shiftsFor($shift));
        }

        $members = $memberQ->get();

        // Load all defined processes for the factory
        $machineProcesses = MachineProcess::where('sc_id', $scId)
            ->where('factory', $factory)
            ->orderBy('id')
            ->get()
            ->groupBy('machine_name');

        // Load semua skill untuk factory ini (keyed by member_id + machine_name + process_name)
        // Since we are changing structure, let's pass the flat collection and filter in the view or restructure here.
        // Let's restructure: skills[member_id][machine_name][process_name] = pct
        $rawSkills = MemberSkill::where('sc_id', $scId)
            ->where('factory', $factory)
            ->get();
            
        $skills = [];
        foreach ($rawSkills as $s) {
            $pName = $s->process_name ?: '-'; // Fallback if no process
            $skills[$s->member_id][$s->machine_name][$pName] = $s;
        }

        return view('admin.skill.index', compact(
            'factories', 'factory', 'shift', 'machines', 'members', 'skills', 'machineProcesses'
        ));
    }

    // GET /admin/skills/export
    public function export(Request $request)
    {
        $user    = Auth::user();
        $scId    = ScContext::id();
        $isSA    = $user->isSuperAdmin();
        $allowed = (array) $user->factory;

        $factoriesQ = Factory::where('sc_id', $scId)->orderBy('order_index');
        if (!$isSA) $factoriesQ->whereIn('name', $allowed);
        $factories = $factoriesQ->get();

        $factory = $request->get('factory', $factories->first()?->name ?? '');
        if (!$isSA && !in_array($factory, $allowed)) {
            $factory = $allowed[0] ?? '';
        }
        $shift = $request->get('shift', 'all');

        $machines = Machine::where('sc_id', $scId)
            ->where('factory', $factory)->orderBy('name')
            ->pluck('name')->unique()->values();

        $memberQ = Member::where('sc_id', $scId)
            ->where('factory', $factory)->where('status', 'active')->orderBy('nama');
        if ($shift !== 'all') $memberQ->whereIn('shift', \App\Services\NonShiftResolver::shiftsFor($shift));
        $members = $memberQ->get();

        $machineProcesses = MachineProcess::where('sc_id', $scId)
            ->where('factory', $factory)->orderBy('id')->get()->groupBy('machine_name');

        $rawSkills = MemberSkill::where('sc_id', $scId)->where('factory', $factory)->get();
        $skills = [];
        foreach ($rawSkills as $s) {
            $pName = $s->process_name ?: '-';
            $skills[$s->member_id][$s->machine_name][$pName] = $s->skill_pct;
        }

        // ── Build column map ──────────────────────────────────────────────
        // Each entry: ['machine' => string, 'process' => string|null]
        $colMap = [];
        foreach ($machines as $mac) {
            $procs = $machineProcesses->get($mac, collect());
            if ($procs->isEmpty()) {
                $colMap[] = ['machine' => $mac, 'process' => null, 'proc_label' => 'ALL'];
            } else {
                foreach ($procs as $p) {
                    $colMap[] = ['machine' => $mac, 'process' => $p->process_name, 'proc_label' => $p->process_name];
                }
            }
        }

        // ── PhpSpreadsheet setup ──────────────────────────────────────────
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $ws = $spreadsheet->getActiveSheet();
        $ws->setTitle('Skill Matrix');
        $ws->setShowGridlines(false);

        // Palette
        $GREEN_DARK  = 'FF185E35';
        $GREEN_MID   = 'FF237048';
        $GREEN_LIGHT = 'FFD1FAE5';
        $NAVY        = 'FF1F3C88';
        $WHITE       = 'FFFFFFFF';
        $GREY_LIGHT  = 'FFF9FAFB';
        $GREY_MID    = 'FFD1D5DB';
        $DARK        = 'FF111827';

        // Skill colour levels
        $SKILL_100_BG  = 'FF1F2937'; $SKILL_100_FG  = 'FFFFFFFF';
        $SKILL_75_BG   = 'FF374151'; $SKILL_75_FG   = 'FFFFFFFF';
        $SKILL_50_BG   = 'FF6B7280'; $SKILL_50_FG   = 'FFFFFFFF';
        $SKILL_25_BG   = 'FFD1D5DB'; $SKILL_25_FG   = 'FF374151';
        $SKILL_0_BG    = 'FFFFFFFF'; $SKILL_0_FG    = 'FFD1D5DB';

        // Helper: column letter from 0-based index
        $col = fn(int $i) => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);

        // Fixed columns: No | Nama | Jabatan | Shift  (0-based idx 0..3)
        $fixedCols = 4;
        $totalCols = $fixedCols + count($colMap);

        // ── Column widths ─────────────────────────────────────────────────
        $ws->getColumnDimensionByColumn(1)->setWidth(5);   // No
        $ws->getColumnDimensionByColumn(2)->setWidth(30);  // Nama
        $ws->getColumnDimensionByColumn(3)->setWidth(22);  // Jabatan
        $ws->getColumnDimensionByColumn(4)->setWidth(10);  // Shift
        for ($i = $fixedCols; $i < $totalCols; $i++) {
            $ws->getColumnDimensionByColumn($i + 1)->setWidth(14);
        }

        // ── ROW 1: Main title ─────────────────────────────────────────────
        $lastCol = $col($totalCols - 1);
        $ws->mergeCells("A1:{$lastCol}1");
        $ws->setCellValue('A1', 'HENKATEN BOARD  —  SKILL MATRIX');
        $ws->getStyle('A1')->applyFromArray([
            'font'      => ['name' => 'Arial', 'bold' => true, 'size' => 16, 'color' => ['argb' => $WHITE]],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $GREEN_DARK]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(1)->setRowHeight(40);

        // ── ROW 2: Subtitle ───────────────────────────────────────────────
        $ws->mergeCells("A2:{$lastCol}2");
        $shiftLabel = $shift === 'all' ? 'Semua Shift' : "Shift {$shift}";
        $ws->setCellValue('A2', "{$factory}  |  {$shiftLabel}  |  Generated: " . now()->format('d/m/Y H:i'));
        $ws->getStyle('A2')->applyFromArray([
            'font'      => ['name' => 'Arial', 'size' => 10, 'color' => ['argb' => 'FFCCCCCC']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $GREEN_MID]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        ]);
        $ws->getRowDimension(2)->setRowHeight(22);

        // ── ROW 3: spacer ─────────────────────────────────────────────────
        $ws->mergeCells("A3:{$lastCol}3");
        $ws->getStyle('A3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB($GREEN_LIGHT);
        $ws->getRowDimension(3)->setRowHeight(6);

        // ── ROWS 4-5: Double header (machine row + process row) ───────────
        // Fixed header cells (rows 4+5 merged)
        $fixedHeaders = ['No', 'Nama Operator', 'Jabatan', 'Shift'];
        foreach ($fixedHeaders as $idx => $label) {
            $c = $col($idx);
            $ws->mergeCells("{$c}4:{$c}5");
            $ws->setCellValue("{$c}4", $label);
            $ws->getStyle("{$c}4:{$c}5")->applyFromArray([
                'font'      => ['name' => 'Arial', 'bold' => true, 'size' => 10, 'color' => ['argb' => $WHITE]],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $GREEN_DARK]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                                 'color' => ['argb' => 'FF114526']]],
            ]);
        }

        // Machine group header row (row 4) — merge consecutive same-machine columns
        $machineGroups = [];
        foreach ($colMap as $i => $cm) {
            $mac = $cm['machine'];
            if (empty($machineGroups) || end($machineGroups)['machine'] !== $mac) {
                $machineGroups[] = ['machine' => $mac, 'start' => $i, 'count' => 1];
            } else {
                $machineGroups[count($machineGroups) - 1]['count']++;
            }
        }

        foreach ($machineGroups as $mg) {
            $cStart = $col($fixedCols + $mg['start']);
            $cEnd   = $col($fixedCols + $mg['start'] + $mg['count'] - 1);
            if ($mg['count'] > 1) {
                $ws->mergeCells("{$cStart}4:{$cEnd}4");
            }
            $ws->setCellValue("{$cStart}4", $mg['machine']);
            $ws->getStyle("{$cStart}4:{$cEnd}4")->applyFromArray([
                'font'      => ['name' => 'Arial', 'bold' => true, 'size' => 10, 'color' => ['argb' => $WHITE]],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $GREEN_DARK]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                                 'color' => ['argb' => 'FF114526']]],
            ]);
        }

        // Process sub-header row (row 5)
        foreach ($colMap as $i => $cm) {
            $c = $col($fixedCols + $i);
            $ws->setCellValue("{$c}5", $cm['proc_label']);
            $ws->getStyle("{$c}5")->applyFromArray([
                'font'      => ['name' => 'Arial', 'bold' => false, 'size' => 9, 'color' => ['argb' => 'FFCFDFD4']],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $GREEN_DARK]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                                 'color' => ['argb' => 'FF114526']]],
            ]);
        }

        $ws->getRowDimension(4)->setRowHeight(22);
        $ws->getRowDimension(5)->setRowHeight(18);

        // ── DATA ROWS (start row 6) ────────────────────────────────────────
        $skillIluo = function(int $pct): string {
            if ($pct >= 100) return '● 100%';
            if ($pct >= 75)  return '◕  75%';
            if ($pct >= 50)  return '◑  50%';
            if ($pct >= 25)  return '◔  25%';
            return '○   0%';
        };

        $skillBg = function(int $pct) use ($SKILL_100_BG, $SKILL_75_BG, $SKILL_50_BG, $SKILL_25_BG, $SKILL_0_BG): string {
            if ($pct >= 100) return $SKILL_100_BG;
            if ($pct >= 75)  return $SKILL_75_BG;
            if ($pct >= 50)  return $SKILL_50_BG;
            if ($pct >= 25)  return $SKILL_25_BG;
            return $SKILL_0_BG;
        };

        $skillFg = function(int $pct) use ($SKILL_100_FG, $SKILL_75_FG, $SKILL_50_FG, $SKILL_25_FG, $SKILL_0_FG): string {
            if ($pct >= 100) return $SKILL_100_FG;
            if ($pct >= 75)  return $SKILL_75_FG;
            if ($pct >= 50)  return $SKILL_50_FG;
            if ($pct >= 25)  return $SKILL_25_FG;
            return $SKILL_0_FG;
        };

        $row = 6;
        foreach ($members as $idx => $m) {
            $rowBg = ($idx % 2 === 0) ? $WHITE : $GREY_LIGHT;

            // Fixed cols
            $fixedData = [$idx + 1, $m->nama, $m->jabatan, $m->shift];
            foreach ($fixedData as $fi => $fval) {
                $c = $col($fi);
                $ws->setCellValue("{$c}{$row}", $fval);
                $ws->getStyle("{$c}{$row}")->applyFromArray([
                    'font'      => ['name' => 'Arial', 'size' => 10,
                                    'bold' => $fi === 1,
                                    'color' => ['argb' => $DARK]],
                    'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $rowBg]],
                    'alignment' => ['horizontal' => $fi === 1
                                        ? \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT
                                        : \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                                     'color' => ['argb' => $GREY_MID]]],
                ]);
            }

            // Skill cols
            foreach ($colMap as $ci => $cm) {
                $pct  = (int) ($skills[$m->id][$cm['machine']][$cm['process'] ?? '-'] ?? 0);
                $c    = $col($fixedCols + $ci);
                $ws->setCellValue("{$c}{$row}", $skillIluo($pct));
                $ws->getStyle("{$c}{$row}")->applyFromArray([
                    'font'      => ['name' => 'Courier New', 'size' => 9, 'bold' => true,
                                    'color' => ['argb' => $skillFg($pct)]],
                    'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'startColor' => ['argb' => $skillBg($pct)]],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                    'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                    'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                                     'color' => ['argb' => $GREY_MID]]],
                ]);
            }

            $ws->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        // ── Footer ────────────────────────────────────────────────────────
        $ws->mergeCells("A{$row}:{$lastCol}{$row}");
        $ws->setCellValue("A{$row}", 'Generated by HENKATEN BOARD  |  ' . now()->format('d/m/Y H:i'));
        $ws->getStyle("A{$row}")->applyFromArray([
            'font'      => ['name' => 'Arial', 'size' => 9, 'color' => ['argb' => '9999']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF3F4F6']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ]);
        $ws->getRowDimension($row)->setRowHeight(16);

        // ── Legend rows ───────────────────────────────────────────────────
        $row += 2;
        $legendData = [
            ['● 100%', 'Mahir / Bisa Gantikan', $SKILL_100_BG, $SKILL_100_FG],
            ['◕  75%', 'Bisa dengan Pengawasan', $SKILL_75_BG, $SKILL_75_FG],
            ['◑  50%', 'Sedang Belajar', $SKILL_50_BG, $SKILL_50_FG],
            ['◔  25%', 'Pengenalan Dasar', $SKILL_25_BG, $SKILL_25_FG],
            ['○   0%', 'Belum Terlatih', $SKILL_0_BG, $SKILL_0_FG],
        ];
        $ws->setCellValue("A{$row}", 'KETERANGAN SKILL');
        $ws->getStyle("A{$row}")->getFont()->setBold(true)->setSize(10);
        $ws->getRowDimension($row)->setRowHeight(18);
        $row++;
        foreach ($legendData as [$symbol, $label, $bg, $fg]) {
            $ws->setCellValue("A{$row}", $symbol);
            $ws->setCellValue("B{$row}", $label);
            $ws->getStyle("A{$row}")->applyFromArray([
                'font'      => ['name' => 'Courier New', 'size' => 9, 'bold' => true, 'color' => ['argb' => $fg]],
                'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => $bg]],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            ]);
            $ws->getStyle("B{$row}")->applyFromArray([
                'font' => ['name' => 'Arial', 'size' => 9],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFFFFF']],
            ]);
            $ws->getRowDimension($row)->setRowHeight(18);
            $row++;
        }

        // Freeze panes after header rows and after fixed columns
        $ws->freezePane($col($fixedCols) . '6');

        // ── Save & stream ─────────────────────────────────────────────────
        $dir = storage_path('app/temp');
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $path = "{$dir}/skill_{$factory}_Shift{$shift}_" . uniqid() . '.xlsx';
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet))->save($path);

        $safeFactory = preg_replace('/[^A-Za-z0-9_\-]/', '_', $factory);
        $filename    = "Skill_Matrix_{$safeFactory}_Shift{$shift}.xlsx";

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // POST /api/skills/save  — simpan satu skill (AJAX)
    public function save(Request $request)
    {
        $request->validate([
            'member_id'    => 'required|integer|exists:members,id',
            'machine_name' => 'required|string|max:100',
            'process_name' => 'nullable|string|max:150',
            'factory'      => 'required|string',
            'skill_pct'    => 'required|integer|min:0|max:100',
        ]);

        $user  = Auth::user();
        $scId  = ScContext::id();

        // Guard: member harus milik SC ini
        $member = Member::where('id', $request->member_id)
            ->where('sc_id', $scId)
            ->firstOrFail();

        if (!$user->isSuperAdmin() && !in_array($member->factory, (array) $user->factory)) {
            abort(403, 'Unauthorized factory access.');
        }

        $skill = MemberSkill::updateOrCreate(
            [
                'sc_id'        => $scId,
                'member_id'    => $member->id,
                'machine_name' => $request->machine_name,
                'process_name' => $request->process_name ?: null,
                'factory'      => $request->factory,
            ],
            [
                'skill_pct'  => $request->skill_pct,
                'updated_by' => $user->id,
            ]
        );

        return response()->json([
            'ok'        => true,
            'skill_pct' => $skill->skill_pct,
            'eligible'  => $skill->isEligibleForReplacement(),
        ]);
    }

    public function saveBatch(Request $request)
    {
        $request->validate([
            'skills' => 'required|array',
            'skills.*.member_id' => 'required|integer',
            'skills.*.machine_name' => 'required|string',
            'skills.*.process_name' => 'nullable|string',
            'skills.*.factory' => 'required|string',
            'skills.*.skill_pct' => 'required|integer|min:0|max:100',
        ]);

        $user = Auth::user();
        $scId = ScContext::id();

        foreach ($request->skills as $s) {
            $member = Member::where('id', $s['member_id'])->where('sc_id', $scId)->first();
            if (!$member || (!$user->isSuperAdmin() && !in_array($member->factory, (array) $user->factory))) {
                continue;
            }

            MemberSkill::updateOrCreate(
                [
                    'sc_id' => $scId,
                    'member_id' => $member->id,
                    'machine_name' => $s['machine_name'],
                    'process_name' => $s['process_name'] ?: null,
                    'factory' => $s['factory'],
                ],
                [
                    'skill_pct' => $s['skill_pct'],
                    'updated_by' => $user->id,
                ]
            );
        }

        return response()->json(['ok' => true]);
    }

    // GET /api/skills  — JSON list untuk TV/slide
    public function apiList(Request $request)
    {
        $scId    = ScContext::id();
        $factory = $request->get('factory', ScContext::firstFactory());
        $shift   = $request->get('shift', 'all');

        $memberQ = \App\Models\Member::where('sc_id', $scId)
            ->where('factory', $factory)
            ->where('status', 'active')
            ->orderBy('nama');

        if ($shift !== 'all') {
            $memberQ->whereIn('shift', \App\Services\NonShiftResolver::shiftsFor($shift));
        }

        $members = $memberQ->get(['id','nama','jabatan','shift']);

        $rawSkills = MemberSkill::where('sc_id', $scId)
            ->where('factory', $factory)
            ->get();

        $skills = [];
        foreach ($rawSkills as $s) {
            $pName = $s->process_name ?: '-';
            $skills[$s->member_id][$s->machine_name][$pName] = ['skill_pct' => $s->skill_pct];
        }

        // We also need to send the machine processes structure to the TV so it knows how to build the columns
        $machineProcesses = MachineProcess::where('sc_id', $scId)
            ->where('factory', $factory)
            ->orderBy('id')
            ->get()
            ->groupBy('machine_name');

        $processes = [];
        foreach ($machineProcesses as $mName => $procs) {
            $processes[$mName] = $procs->pluck('process_name')->toArray();
        }

        return response()->json(compact('members', 'skills', 'processes'));
    }

    // POST /api/skills/processes/add
    public function addProcess(Request $request)
    {
        $request->validate([
            'factory'      => 'required|string',
            'machine_name' => 'required|string|max:100',
            'process_name' => 'required|string|max:150',
        ]);

        $user = Auth::user();
        if (!$user->isSuperAdmin() && !in_array($request->factory, (array) $user->factory)) {
            abort(403);
        }

        $process = MachineProcess::create([
            'sc_id'        => ScContext::id(),
            'factory'      => $request->factory,
            'machine_name' => $request->machine_name,
            'process_name' => $request->process_name,
        ]);

        return response()->json(['ok' => true, 'process' => $process]);
    }

    // POST /api/skills/processes/delete
    public function deleteProcess(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:machine_processes,id',
        ]);

        $process = MachineProcess::findOrFail($request->id);
        $user = Auth::user();
        if (!$user->isSuperAdmin() && !in_array($process->factory, (array) $user->factory)) {
            abort(403, 'Unauthorized factory');
        }

        // Optional: Delete all member skills for this specific process
        MemberSkill::where('sc_id', $process->sc_id)
            ->where('machine_name', $process->machine_name)
            ->where('process_name', $process->process_name)
            ->delete();

        $process->delete();
        return response()->json(['ok' => true]);
    }
}
