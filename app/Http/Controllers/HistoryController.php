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
            $perPage = (int) $request->input('per_page', 10);
            $perPage = max(1, min($perPage, 100));
            $key = $request->input('key');
            $order = strtolower($request->input('order', 'asc'));
            $user_id = $request->user()->id;
            
            $historyQuery = History::query();
            
            $allowedKeys = ['material_types.name', 'created_at'];
            
            if (in_array($key, $allowedKeys) && in_array($order, ['asc', 'desc'])) {
                if ($key === 'material_types.name') {
                    // Hacemos join solo si se ordena por nombre de categoría
                    $historyQuery->join('material_types', 'history.material_type_id', '=', 'material_types.id')
                                  ->orderBy('material_types.name', $order);
                } else {
                    $historyQuery->orderBy($key, $order);
                }
            }

            $histories = $historyQuery->with('alliance')->with('materialType')->with('reward')->with('scan')->where('user_id', $user_id)->paginate($perPage);
            // $data = $this->unsetDataPagination($histories);
            return $this->apiResponse(true, 'Historial obtenido exitosamente.', $histories, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener el historial.', null, $e->getMessage() . ' ' . $e->getLine(), 500);
        }
    }

    public function logHistory($user_id, $alliance_id, $material_type_id, $reward_id, $type_history, $scan_id, $points)
    {

        $request = new Request([
            'user_id' => $user_id,
            'alliance_id' => $alliance_id,
            'material_type_id' => $material_type_id,
            'reward_id' => $reward_id,
            'type_history' => $type_history,
            'scan_id' => $scan_id,
            'points' => $points,
        ]);

        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'alliance_id' => 'nullable|exists:alliances,id',
                'material_type_id' => 'nullable|exists:material_types,id',
                'reward_id' => 'nullable|exists:rewards,id',
                'type_history' => 'required|in:1,2',
                'scan_id' => 'nullable|exists:scans,id',
                'points' => 'required|numeric',
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

            // si es tipo 1, validar que solo haya enviado alliance_id
            if ($request->type_history == 1 && $request->alliance_id == null ) {
                throw ValidationException::withMessages([
                    'alliance_id' => 'El campo comercio es obligatorio.',
                ]);
            }

            $history = History::create([
                'user_id' => $request->user_id,
                'alliance_id' => $request->type_history == 1 ? $request->alliance_id : null,
                'material_type_id' => $request->type_history == 2 ? $request->material_type_id : null,
                'reward_id' => $request->type_history == 2 ? $request->reward_id : null,
                'type_history' => $request->type_history,
                'scan_id' => $request->scan_id,
                'points' => $request->points,
            ]);

            return $history;
            
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al registrar el historial.', null, $e->getMessage(), 500);
        }
    }
}
