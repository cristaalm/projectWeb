<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\History;
use App\Models\Alliance;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function getAll(Request $request)
    {
        try {
            $normalize = function ($value) {
                if (is_string($value) && strtolower($value) === 'null') {
                    return null;
                }
                return $value;
            };
    
            $perPage = (int) ($normalize($request->input('per_page')) ?? 10);
            $perPage = max(1, min($perPage, 100));
            $key = $normalize($request->input('key')) ?? 'created_at';
            $order = strtolower($normalize($request->input('order')) ?? 'desc');
            $user = $request->user();
            
            $historyQuery = History::query();
            
            $allowedKeys = ['material_types.name', 'created_at', 'created_at'];
            
            if (in_array($key, $allowedKeys) && in_array($order, ['asc', 'desc'])) {
                if ($key === 'material_types.name') {
                    // Hacemos join solo si se ordena por nombre de categoría
                    $historyQuery->join('material_types', 'history.material_type_id', '=', 'material_types.id')
                                  ->orderBy('material_types.name', $order);
                } else {
                    $historyQuery->orderBy($key, $order);
                }
            }

            if ($user->role_id == 4) {
                $historyQuery->where('comerciant_id', $user->id);
                $historyQuery->with('user');
            } else {
                $historyQuery->where('user_id', $user->id);
                $historyQuery->with('comerciant');
            }

            $histories = $historyQuery->with('alliance')->with('materialType')->with('reward')->with('scan')->paginate($perPage);
            return $this->apiResponse(true, 'Historial obtenido exitosamente.', $histories, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener el historial.', null, $e->getMessage() . ' ' . $e->getLine(), 500);
        }
    }

    public function getAllSystem(Request $request)
    {
        try {
            $normalize = function ($value) {
                if (is_string($value) && strtolower($value) === 'null') {
                    return null;
                }
                return $value;
            };
    
            $perPage = (int) ($normalize($request->input('per_page')) ?? 10);
            $perPage = max(1, min($perPage, 100));
            $key = $normalize($request->input('key')) ?? 'created_at';
            $order = strtolower($normalize($request->input('order')) ?? 'desc');
            $type_history = $normalize($request->input('type_history')) ?? null;
            
            $historyQuery = History::query();
            
            $allowedKeys = ['created_at', 'created_at'];
            
            if (in_array($key, $allowedKeys) && in_array($order, ['asc', 'desc'])) {
                $historyQuery->orderBy($key, $order);
            }

            if ($type_history) {
                $historyQuery->where('type_history', $type_history);
            }
            $histories = $historyQuery->with('reward')->with('user')->paginate($perPage);
            return $this->apiResponse(true, 'Historial obtenido exitosamente.', $histories, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener el historial.', null, $e->getMessage() . ' ' . $e->getLine(), 500);
        }
    }

    public function totalPointsByShop(Request $request, int $alliance_id) 
    {
        try {
            $request->merge([
                'alliance_id' => $alliance_id,
            ]);

            $validateData = $request->validate([
                'alliance_id' => 'required|exists:alliances,id',
                'date_start' => 'required|date',
                'date_end' => 'required|date',
            ]);

            $totalPoints = History::where('alliance_id', $validateData['alliance_id'])
                ->where('type_history', 1)
                ->whereBetween('created_at', [$validateData['date_start'], $validateData['date_end']])
                ->sum('points');
            return $this->apiResponse(true, 'Total de puntos obtenido exitosamente.', ['total_points' => $totalPoints * -1], null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener el historial.', null, $e->getMessage() . ' ' . $e->getLine(), 500);
        }
    }

    public function logHistory($user_id = null, $comerciant_id = null, $alliance_id = null, $material_type_id = null, $reward_id = null, $type_history = null, $scan_id = null, $quantity = null, $points = null, $description = null)
    {

        try {
            $history = History::create([
                'user_id' => $user_id,
                'comerciant_id' => $comerciant_id,
                'alliance_id' => $alliance_id,
                'material_type_id' => $material_type_id,
                'reward_id' => $reward_id,
                'type_history' => $type_history,
                'scan_id' => $scan_id,
                'quantity' => $quantity,
                'points' => $points,
                'description' => $description,
            ]);

            return $history;
            
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al registrar el historial.', null, $e->getMessage(), 500);
        }
    }

    //Nueva funcion para mostrar en la grafica
    public function getTopAlliancesByRedemptions(Request $request)
    {
        try {
        // Paso 1: Obtener los 7 alliance_id con más canjeos (type_history = 1)
        $topAlliances = History::query()
            ->where('type_history', 1)
            ->selectRaw('alliance_id, SUM(quantity) as total_quantity')
            ->groupBy('alliance_id')
            ->orderByDesc('total_quantity')
            ->limit(7)
            ->pluck('total_quantity', 'alliance_id'); // [alliance_id => total_quantity]

        if ($topAlliances->isEmpty()) {
            return response()->json([]);
        }

        // Paso 2: Cargar los modelos Alliance correspondientes
        $alliances = Alliance::find(array_keys($topAlliances->toArray()));

        // Paso 3: Formatear la respuesta
        $result = $alliances->map(function (Alliance $alliance) use ($topAlliances) {
            return [
                'alliance_id' => $alliance->id,
                'alliance' => $alliance,
                'quantity' => (int) $topAlliances[$alliance->id],
            ];
        })->values();

        return $this->apiResponse(true, 'Lista de los 7 comercios con más canjeos obtenida correctamente.', $result, null, 200);

        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener los comercios con más canjeos.', null, $e->getMessage(), 500);
        }
    }
}
