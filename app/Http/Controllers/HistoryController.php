<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\History;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

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

    public function logHistory($user_id = null, $comerciant_id = null, $alliance_id = null, $material_type_id = null, $reward_id = null, $type_history = null, $scan_id = null, $quantity = null, $points = null, $description = null)
    {

        $request = new Request([
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

        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'comerciant_id' => 'nullable|exists:users,id',
                'alliance_id' => 'nullable|exists:alliances,id',
                'material_type_id' => 'nullable|exists:material_types,id',
                'reward_id' => 'nullable|exists:rewards,id',
                'type_history' => 'required|in:1,2,3',
                'scan_id' => 'nullable|exists:scans,id',
                'quantity' => 'required|numeric',
                'points' => 'required|numeric',
                'description' => 'nullable|string',
            ]);
            
            // si es tipo 2, validar que solo haya enviado material_type_id
            if ($request->type_history == 2 && $request->material_type_id == null ) {
                throw ValidationException::withMessages([
                    'material_type_id' => 'El campo tipo de material es obligatorio.',
                ]);
            }

            if ($request->type_history == 2 && $request->scan_id == null ) {
                throw ValidationException::withMessages([
                    'scan_id' => 'El campo escaneo es obligatorio.',
                ]);
            }
            
            if ($request->type_history == 1 && $request->alliance_id == null ) {
                throw ValidationException::withMessages([
                    'alliance_id' => 'El campo comercio es obligatorio.',
                ]);
            }

            if ($request->type_history == 1 && $request->reward_id == null ) {
                throw ValidationException::withMessages([
                    'reward_id' => 'El campo recompensa es obligatorio.',
                ]);
            }

            if ($request->type_history == 3 && $request->description == null ) {
                throw ValidationException::withMessages([
                    'description' => 'El campo descripción es obligatorio.',
                ]);
            }

            $history = History::create([
                'user_id' => $request->user_id,
                'comerciant_id' => $request->comerciant_id,
                'alliance_id' => $request->alliance_id,
                'material_type_id' => $request->material_type_id,
                'reward_id' => $request->reward_id,
                'type_history' => $request->type_history,
                'scan_id' => $request->scan_id,
                'quantity' => $request->quantity,
                'points' => $request->points,
                'description' => $request->description,
            ]);

            return $history;
            
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al registrar el historial.', null, $e->getMessage(), 500);
        }
    }
}
