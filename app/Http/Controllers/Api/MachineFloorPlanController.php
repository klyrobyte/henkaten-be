<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\ProblemLog;
use App\Services\ScContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MachineFloorPlanController extends Controller
{
    /**
     * Get ALL machines (for dropdown/selection, not floor plan display)
     * GET /api/machines/all
     * 
     * Returns all machines regardless of coordinates
     * Used for: floor plan editor dropdown, machine selection, etc
     */
    public function getAllMachines(Request $request)
    {
        try {
            $scId   = ScContext::id();
            $factory = $request->get('factory') ?? ScContext::firstFactory();
            if (empty($factory)) {
                return response()->json(['message' => 'No factory configured for this Service Center.'], 422);
            }

            // Get all machines from this factory scoped to the active SC
            $machines = Machine::where('sc_id', $scId)
                ->where('factory', $factory)
                ->orderBy('status')
                ->orderBy('name')
                ->get();

            // Map to response format
            $result = $machines->map(function(Machine $machine) {
                $typeLabel = match($machine->status) {
                    'mesin' => 'Machine',
                    'robot' => 'Robot',
                    'pos' => 'POS',
                    'line' => 'Line',
                    'persons' => 'Persons',
                    'lainya' => 'Others',
                    'mc_vibration' => 'MC Vibration',
                    default => $machine->status
                };

                return [
                    'id' => $machine->id,
                    'name' => $machine->name,
                    'factory' => $machine->factory,
                    'type' => $machine->status,
                    'type_label' => $typeLabel,
                    'floor_cx' => (float) $machine->floor_cx,
                    'floor_cy' => (float) $machine->floor_cy,
                    'has_coordinates' => !is_null($machine->floor_cx) && !is_null($machine->floor_cy),
                ];
            });

            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('MachineFloorPlanController::getAllMachines() error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return response()->json(['message' => 'Unable to retrieve machine data.'], 500);
        }
    }

    /**
     * Get machines with floor plan coordinates and current status
     * GET /api/machines/floor-plan
     * 
     * Uses the same logic as dashboard:
     * - Status based on OPEN problem logs (Machine, Material, Method)
     * - Not based on MachineStatus table (which is for shift tracking)
     * - Only returns machines with floor plan coordinates
     * - Machines with status "lainya" won't show unless they have coordinates
     */
    public function getFloorPlanData(Request $request)
    {
        try {
            $scId   = ScContext::id();
            $factory = $request->get('factory') ?? ScContext::firstFactory();
            if (empty($factory)) {
                return response()->json(['message' => 'No factory configured for this Service Center.'], 422);
            }
            $tanggal = $request->get('tanggal', today()->toDateString());
            $shift = $request->get('shift', 'A');

            // Get all machines with coordinates from this factory scoped to the active SC
            $machines = Machine::where('sc_id', $scId)
                ->where('factory', $factory)
                ->whereNotNull('floor_cx')
                ->whereNotNull('floor_cy')
                ->orderBy('name')
                ->get();

            // Get all OPEN problem logs for today/shift scoped to the active SC
            $openLogsByMachine = ProblemLog::where([
                'sc_id'  => $scId,
                'tanggal' => $tanggal,
                'factory' => $factory,
                'shift'   => $shift,
                'status'  => 'open',
            ])->whereIn('jenis', ['Machine', 'Material', 'Method'])
                ->get()
                ->groupBy('lokasi')  // 'lokasi' is the machine name field
                ->map(fn($logs) => $logs->pluck('jenis')
                    ->map(fn($j) => strtolower($j))
                    ->unique()->values()->toArray());

            // Map to response format
            $result = $machines->map(function(Machine $machine) use ($openLogsByMachine) {
                $problemStatuses = $openLogsByMachine[$machine->name] ?? [];
                
                // Determine status based on open problem logs
                if (!empty($problemStatuses)) {
                    // Has problems - show red
                    $status = 'ada_masalah';  // "problem" status
                } else {
                    // No problems - show green
                    $status = 'ok';  // "safe" status
                }

                return [
                    'id' => $machine->id,
                    'name' => $machine->name,
                    'factory' => $machine->factory,
                    'type' => $machine->status,  // machine type (mesin, robot, pos, etc)
                    'floor_cx' => (float) $machine->floor_cx,
                    'floor_cy' => (float) $machine->floor_cy,
                    'status' => $status,
                    'problem_types' => $problemStatuses,  // ['machine', 'material', 'method', etc]
                    'updated_at' => $machine->updated_at,
                ];
            });

            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('MachineFloorPlanController::getFloorPlanData() error', [
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
            ]);
            return response()->json(['message' => 'Unable to retrieve floor plan data.'], 500);
        }
    }

    /**
     * Get a single machine's floor plan data
     * GET /api/machines/{id}/floor-plan
     */
    public function getFloorPlanDataById($id)
    {
        try {
            $machine = Machine::findOrFail($id);

            if (!$machine->floor_cx || !$machine->floor_cy) {
                return response()->json([
                    'error' => 'Machine has no floor coordinates'
                ], 404);
            }

            $tanggal = request()->get('tanggal', today()->toDateString());
            $shift = request()->get('shift', 'A');

            $problemLog = ProblemLog::where([
                'lokasi' => $machine->name,
                'factory' => $machine->factory,
                'tanggal' => $tanggal,
                'shift' => $shift,
                'status' => 'open',
            ])->whereIn('jenis', ['Machine', 'Material', 'Method'])->first();

            return response()->json([
                'id' => $machine->id,
                'name' => $machine->name,
                'factory' => $machine->factory,
                'floor_cx' => (float) $machine->floor_cx,
                'floor_cy' => (float) $machine->floor_cy,
                'status' => $problemLog ? 'ada_masalah' : 'ok',
                'has_problem' => (bool) $problemLog,
                'updated_at' => $machine->updated_at,
            ]);
        } catch (\Exception $e) {
            \Log::error('MachineFloorPlanController::getFloorPlanDataById() error', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return response()->json(['message' => 'Unable to retrieve machine data.'], 500);
        }
    }
}
