<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProblemLog;
use App\Models\AbsenceRecord;
use App\Models\AbsenceSummary;
use App\Models\AbsenceReason;
use App\Models\AssignmentReplacement;
use App\Models\DailyAssignment;
use App\Models\MachineStatus;
use App\Models\GlobalLog;
use App\Models\Member;
use App\Services\FactoryConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MasterDataController extends Controller
{
    public function __construct(
        protected FactoryConfigService $factoryConfig
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $factories = $this->factoryConfig->getFactoryObjects();

        if (!$user->isSuperAdmin()) {
            $allowedFactories = (array) $user->factory;
            $factories = $factories->filter(fn($f) => in_array($f->name, $allowedFactories));
        }

        $defaultFactory = $factories->first()?->name ?? 'Factory 2';
        $factory = $request->get('factory', $request->session()->get('factory', $defaultFactory));
        $shift = $request->get('shift', $request->session()->get('shift', 'A'));
        
        // Save to session for persistence
        session(['factory' => $factory, 'shift' => $shift]);

        $mode = $request->get('mode');
        if (!$mode) {
            if ($request->has('dari') && $request->has('sampai')) {
                $mode = 'rentang';
            } elseif ($request->has('bulan')) {
                $mode = 'bulan';
            } elseif ($request->has('tanggal')) {
                $mode = 'hari';
            } else {
                $mode = 'hari';
            }
        }

        $tanggal = $request->get('tanggal', today()->toDateString());

        if ($mode === 'bulan') {
            $bulan = $request->get('bulan', today()->format('Y-m'));
            $dari = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->startOfMonth()->toDateString();
            $sampai = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->endOfMonth()->toDateString();
        } elseif ($mode === 'rentang') {
            $dari = $request->get('dari', \Carbon\Carbon::parse($tanggal)->startOfMonth()->toDateString());
            $sampai = $request->get('sampai', \Carbon\Carbon::parse($tanggal)->endOfMonth()->toDateString());
            if ($dari > $sampai) [$dari, $sampai] = [$sampai, $dari];
            $bulan = \Carbon\Carbon::parse($dari)->format('Y-m');
        } else {
            $mode = 'hari';
            $dari = $tanggal;
            $sampai = $tanggal;
            $bulan = \Carbon\Carbon::parse($tanggal)->format('Y-m');
        }

        $tab = $request->get('tab', '3m');
        $perPage = $request->get('per_page', 15);

        // ── 4M Summary Data ───────────────────────────────────────────
        $logsQuery = ProblemLog::where('factory', $factory)
            ->where('shift', $shift)
            ->whereBetween('tanggal', [$dari, $sampai]);
        
        $summaryLogs = (clone $logsQuery)->get();
        $jenisList = $this->getDynamicJenis();

        $manCount = AbsenceRecord::where('factory', $factory)
            ->where('shift', $shift)
            ->where('status', 'absen')
            ->whereBetween('tanggal', [$dari, $sampai])
            ->distinct('member_id')
            ->count('member_id');

        $replacementsCount = AssignmentReplacement::where('factory', $factory)
            ->where('shift', $shift)
            ->whereBetween('tanggal', [$dari, $sampai])
            ->count();

        // ── History Data ──────────────────────────────────────────────
        switch ($tab) {
            case 'absence':
                $history = AbsenceRecord::with('member')
                    ->where('factory', $factory)
                    ->where('shift', $shift)
                    ->where('status', 'absen')
                    ->whereBetween('tanggal', [$dari, $sampai])
                    ->orderBy('tanggal', 'desc');
                break;
            case 'abs-sum':
                $history = AbsenceSummary::where('factory', $factory)
                    ->where('shift', $shift)
                    ->whereBetween('tanggal', [$dari, $sampai])
                    ->orderBy('tanggal', 'desc');
                break;
            case 'abs-reason':
                $history = AbsenceReason::orderBy('name');
                break;
            case 'replacements':
                $history = AssignmentReplacement::with('member')
                    ->where('factory', $factory)
                    ->where('shift', $shift)
                    ->whereBetween('tanggal', [$dari, $sampai])
                    ->orderBy('tanggal', 'desc');
                break;
            case 'assignments':
                $history = DailyAssignment::with('member')
                    ->where('factory', $factory)
                    ->where('shift', $shift)
                    ->whereBetween('tanggal', [$dari, $sampai])
                    ->orderBy('tanggal', 'desc');
                break;
            case 'mc-status':
                $history = MachineStatus::where('factory', $factory)
                    ->where('shift', $shift)
                    ->whereBetween('tanggal', [$dari, $sampai])
                    ->orderBy('tanggal', 'desc');
                break;
            case 'global-logs':
                $history = GlobalLog::orderBy('created_at', 'desc');
                break;
            case '3m':
            default:
                $tab = '3m';
                $history = (clone $logsQuery)->orderBy('tanggal', 'desc')
                    ->orderBy('waktu_mulai', 'desc');
                break;
        }

        $history = $history->paginate($perPage)->withQueryString();

        if ($tab === 'global-logs') {
            foreach ($history as $log) {
                $log->username_dec = $log->decrypted_username;
                $log->ip_dec = $log->decrypted_ip;
                $log->user_id_dec = $log->decrypted_user_id;
            }
        }

        $currentFactory = $factories->firstWhere('name', $factory) ?? $factories->first();
        $members = Member::orderBy('nama')->get();

        return view('admin.superadmin.master_data', compact(
            'factories', 'currentFactory', 'factory', 'shift', 'mode', 'tanggal', 'dari', 'sampai', 'bulan',
            'tab', 'history', 'summaryLogs', 'jenisList', 'manCount', 'replacementsCount', 'perPage', 'members'
        ));
    }

    private function getDynamicJenis(): array
    {
        try {
            $type = DB::select("SHOW COLUMNS FROM problem_logs WHERE Field = 'jenis'")[0]->Type;
            preg_match('/^enum\((.*)\)$/', $type, $matches);
            $types = [];
            foreach (explode(',', $matches[1]) as $value) {
                $types[] = trim($value, "'");
            }
            return $types;
        } catch (\Exception $e) {
            return ['Machine', 'Material', 'Method'];
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $type = $request->get('type', '3m');
        switch ($type) {
            case '3m':
                ProblemLog::findOrFail($id)->delete();
                return response()->json(['ok' => true, 'message' => 'Problem Log deleted successfully.']);
            case 'absence':
                AbsenceRecord::findOrFail($id)->delete();
                return response()->json(['ok' => true, 'message' => 'Absence Record deleted successfully.']);
            case 'abs-sum':
                AbsenceSummary::findOrFail($id)->delete();
                return response()->json(['ok' => true, 'message' => 'Absence Summary deleted successfully.']);
            case 'abs-reason':
                AbsenceReason::findOrFail($id)->delete();
                return response()->json(['ok' => true, 'message' => 'Absence Reason deleted successfully.']);
            case 'replacements':
                AssignmentReplacement::findOrFail($id)->delete();
                return response()->json(['ok' => true, 'message' => 'Replacement Record deleted successfully.']);
            case 'assignments':
                DailyAssignment::findOrFail($id)->delete();
                return response()->json(['ok' => true, 'message' => 'Daily Assignment deleted successfully.']);
            case 'mc-status':
                MachineStatus::findOrFail($id)->delete();
                return response()->json(['ok' => true, 'message' => 'Machine Status deleted successfully.']);
            case 'global-logs':
                GlobalLog::findOrFail($id)->delete();
                return response()->json(['ok' => true, 'message' => 'Global Log deleted successfully.']);
            default:
                return response()->json(['ok' => false, 'message' => 'Invalid resource type.'], 400);
        }
    }

    public function destroyAll(Request $request)
    {
        $tab = $request->get('tab', '3m');
        $factory = $request->get('factory', $request->session()->get('factory'));
        $shift = $request->get('shift', $request->session()->get('shift'));
        
        $mode = $request->get('mode');
        $hasDateFilter = false;
        $dari = $sampai = null;

        if ($mode === 'bulan') {
            $hasDateFilter = true;
            $bulan = $request->get('bulan', today()->format('Y-m'));
            $dari = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->startOfMonth()->toDateString();
            $sampai = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->endOfMonth()->toDateString();
        } elseif ($mode === 'rentang') {
            $hasDateFilter = true;
            $dari = $request->get('dari');
            $sampai = $request->get('sampai');
            if ($dari && $sampai && $dari > $sampai) [$dari, $sampai] = [$sampai, $dari];
        } elseif ($request->has('tanggal')) {
            $hasDateFilter = true;
            $dari = $sampai = $request->get('tanggal');
        }

        // Base query builder based on tab
        $query = match ($tab) {
            '3m'           => ProblemLog::query(),
            'absence'      => AbsenceRecord::query(),
            'abs-sum'      => AbsenceSummary::query(),
            'abs-reason'   => null, // Uses truncate
            'replacements' => AssignmentReplacement::query(),
            'assignments'  => DailyAssignment::query(),
            'mc-status'    => MachineStatus::query(),
            'global-logs'  => null, // Uses truncate
            default        => null,
        };

        if ($tab === 'abs-reason' || $tab === 'global-logs') {
            $model = $tab === 'abs-reason' ? AbsenceReason::class : GlobalLog::class;
            $count = $model::count();
            $model::truncate();
            return response()->json(['ok' => true, 'message' => "Successfully cleared all {$count} " . str_replace('-', ' ', $tab) . " records."]);
        }

        if (!$query) {
            return response()->json(['ok' => false, 'message' => 'Invalid resource type.'], 400);
        }

        // Apply shared filters
        if ($factory) $query->where('factory', $factory);
        if ($shift)   $query->where('shift', $shift);
        if ($hasDateFilter && $dari && $sampai) {
            $query->whereBetween('tanggal', [$dari, $sampai]);
        }

        // Special case: absence tab in management usually only shows/deletes status='absen'
        if ($tab === 'absence') {
            $query->where('status', 'absen');
        }

        $count = $query->count();
        $query->delete();

        $scope = $hasDateFilter ? "between {$dari} and {$sampai}" : "for all time";
        $msg = "Successfully deleted all {$count} {$tab} records {$scope}";
        if ($factory) $msg .= " in {$factory}";
        if ($shift)   $msg .= " (Shift {$shift})";

        return response()->json(['ok' => true, 'message' => $msg . "."]);
    }

    public function update(Request $request, $id)
    {
        $type = $request->get('type', '3m');
        switch ($type) {
            case '3m':
                $log = ProblemLog::findOrFail($id);
                $validated = $request->validate([
                    'tanggal' => 'required|date',
                    'factory' => 'required|string',
                    'shift' => 'required|string|in:A,B',
                    'jenis' => 'required|string',
                    'lokasi' => 'required|string',
                    'waktu_mulai' => 'required|string',
                    'waktu_selesai' => 'nullable|string',
                    'deskripsi' => 'required|string',
                    'cause' => 'nullable|string',
                    'countermeasure' => 'nullable|string',
                    'pic' => 'nullable|string',
                    'status' => 'required|string|in:open,closed',
                ]);
                
                if (!empty($validated['waktu_mulai']) && !empty($validated['waktu_selesai'])) {
                    $validated['durasi'] = $this->calcDuration($validated['waktu_mulai'], $validated['waktu_selesai']);
                } elseif ($validated['status'] === 'open') {
                    $validated['durasi'] = null;
                    $validated['waktu_selesai'] = null;
                }
                
                $log->update($validated);
                return response()->json(['ok' => true, 'message' => 'Problem Log updated.', 'data' => $log]);

            case 'absence':
                $absence = AbsenceRecord::findOrFail($id);
                $validated = $request->validate([
                    'tanggal' => 'required|date',
                    'factory' => 'required|string',
                    'shift' => 'required|string|in:A,B',
                    'reason' => 'required|string',
                ]);
                $absence->update($validated);
                return response()->json(['ok' => true, 'message' => 'Absence Record updated.', 'data' => $absence]);

            case 'abs-sum':
                $sum = AbsenceSummary::findOrFail($id);
                $validated = $request->validate([
                    'tanggal' => 'required|date',
                    'factory' => 'required|string',
                    'shift' => 'required|string|in:A,B',
                    'mp_hadir' => 'required|integer',
                    'total_absen' => 'required|integer',
                    'total_member' => 'required|integer',
                    'op_cuti' => 'nullable|integer',
                    'op_sakit' => 'nullable|integer',
                    'op_ijin' => 'nullable|integer',
                    'op_Alpha' => 'nullable|integer',
                    'spv_cuti' => 'nullable|integer',
                    'spv_sakit' => 'nullable|integer',
                    'spv_ijin' => 'nullable|integer',
                    'spv_Alpha' => 'nullable|integer',
                    'source' => 'nullable|string',
                ]);
                $sum->update($validated);
                return response()->json(['ok' => true, 'message' => 'Absence Summary updated.', 'data' => $sum]);

            case 'abs-reason':
                $reason = AbsenceReason::findOrFail($id);
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'color' => 'required|string|max:20',
                ]);
                $reason->update($validated);
                return response()->json(['ok' => true, 'message' => 'Absence Reason updated.', 'data' => $reason]);

            case 'replacements':
                $repl = AssignmentReplacement::findOrFail($id);
                $validated = $request->validate([
                    'tanggal' => 'required|date',
                    'factory' => 'required|string',
                    'shift' => 'required|string|in:A,B',
                    'target_machine' => 'required|string',
                    'member_id' => 'required|exists:members,id',
                    'source_machine' => 'required|string',
                ]);
                $repl->update($validated);
                return response()->json(['ok' => true, 'message' => 'Replacement updated.', 'data' => $repl]);

            case 'assignments':
                $assign = DailyAssignment::findOrFail($id);
                $validated = $request->validate([
                    'tanggal' => 'required|date',
                    'factory' => 'required|string',
                    'shift' => 'required|string|in:A,B',
                    'group_title' => 'required|string',
                    'machine_name' => 'required|string',
                    'slot_index' => 'required|integer',
                    'member_id' => 'nullable|exists:members,id',
                    'member_name' => 'nullable|string',
                    'status' => 'required|string',
                    'absent_reason' => 'nullable|string',
                    'is_substitute' => 'boolean',
                    'substitute_for' => 'nullable|integer',
                ]);
                $assign->update($validated);
                return response()->json(['ok' => true, 'message' => 'Daily Assignment updated.', 'data' => $assign]);

            case 'mc-status':
                $mc = MachineStatus::findOrFail($id);
                $validated = $request->validate([
                    'tanggal' => 'required|date',
                    'factory' => 'required|string',
                    'shift' => 'required|string|in:A,B',
                    'machine_name' => 'required|string',
                    'status' => 'required|string',
                ]);
                $mc->update($validated);
                return response()->json(['ok' => true, 'message' => 'Machine Status updated.', 'data' => $mc]);

            case 'global-logs':
                $log = GlobalLog::findOrFail($id);
                $validated = $request->validate([
                    'action' => 'required|string',
                    'target' => 'required|string',
                    'detail' => 'nullable|string',
                    'factory' => 'nullable|string',
                ]);
                $log->update($validated);
                return response()->json(['ok' => true, 'message' => 'Global Log updated.', 'data' => $log]);

            default:
                return response()->json(['ok' => false, 'message' => 'Invalid resource type.'], 400);
        }
    }

    /**
     * Hitung durasi antara dua waktu HH:mm.
     */
    private function calcDuration(string $start, string $end): string
    {
        $start = substr(trim($start), 0, 5);
        $end = substr(trim($end), 0, 5);
        $s = strtotime("2000-01-01 {$start}");
        $e = strtotime("2000-01-01 {$end}");
        $diff = $e - $s;
        if ($diff < 0) $diff += 86400;
        $h = intdiv($diff, 3600);
        $m = intdiv($diff % 3600, 60);
        if ($h > 0 && $m > 0) return "{$h}j {$m}m";
        if ($h > 0) return "{$h}j";
        return "{$m}m";
    }

    public function show($id)
    {
        $type = request('type', '3m');
        switch ($type) {
            case '3m':
                return response()->json(ProblemLog::findOrFail($id));
            case 'absence':
                return response()->json(AbsenceRecord::with('member')->findOrFail($id));
            case 'abs-sum':
                return response()->json(AbsenceSummary::findOrFail($id));
            case 'abs-reason':
                return response()->json(AbsenceReason::findOrFail($id));
            case 'replacements':
                return response()->json(AssignmentReplacement::with('member')->findOrFail($id));
            case 'assignments':
                return response()->json(DailyAssignment::with('member')->findOrFail($id));
            case 'mc-status':
                return response()->json(MachineStatus::findOrFail($id));
            case 'global-logs':
                $log = GlobalLog::findOrFail($id);
                $log->username_dec = $log->decrypted_username;
                $log->ip_dec = $log->decrypted_ip;
                $log->user_id_dec = $log->decrypted_user_id;
                return response()->json($log);
            default:
                return response()->json(['error' => 'Invalid resource type'], 400);
        }
    }
}
