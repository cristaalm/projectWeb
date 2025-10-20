<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Reward;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class RewardController extends Controller
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
            $query = $normalize($request->input('query')) ?? '';
            $key = $normalize($request->input('key')) ?? 'updated_at';
            $order = strtolower($normalize($request->input('order')) ?? 'desc');
            $status = $normalize($request->input('status'));
            $allianceId = $normalize($request->input('alliance_id'));

            $rewardQuery = Reward::query();

            if (!empty($query)) {
                $rewardQuery->where(function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                      ->orWhere('description', 'like', '%' . $query . '%')
                      ->orWhere('points_required', 'like', '%' . $query . '%')
                      ->orWhere('stock', 'like', '%' . $query . '%')
                      ->orWhereHas('alliance', function ($subQ) use ($query) {
                          $subQ->where('name', 'like', '%' . $query . '%');
                      });
                });
            }
            
            if ($status != null && in_array($status, [0, 1])) {
                $rewardQuery->where('is_active', $status);
            }

            if (!empty($allianceId)) {
                $rewardQuery->where('alliance_id', $allianceId);
            }
            
            $allowedKeys = ['name', 'description', 'stock', 'points_required', 'status', 'alliance.name', 'updated_at'];
            
            if (in_array($key, $allowedKeys) && in_array($order, ['asc', 'desc'])) {
                if ($key === 'alliance.name') {
                    $rewardQuery->join('alliances', 'rewards.alliance_id', '=', 'alliances.id')
                    ->orderBy('alliances.name', $order);
                } else {
                    $rewardQuery->orderBy($key, $order);
                }
            }
            
            $rewards = $rewardQuery->with('alliance')->paginate($perPage);
            return $this->apiResponse(true, 'Recompensas obtenidos exitosamente.', $rewards, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener las recompensas.', null, $e->getMessage(), 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'alliance_id' => 'required|exists:alliances,id',
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'points_required' => 'required|integer',
                'stock' => 'nullable|integer',
                'is_active' => 'required|boolean',
                'expires_at' => 'nullable|date',
            ]);

            $digits12 = str_pad(random_int(0, 999999999999), 12, '0', STR_PAD_LEFT);
            $checkDigit = Reward::calculateEan13CheckDigit($digits12);
            $reward = Reward::create([
                'alliance_id' => $validatedData['alliance_id'],
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'points_required' => $validatedData['points_required'],
                'stock' => $validatedData['stock'],
                'code' => $digits12 . $checkDigit,
                'is_active' => $validatedData['is_active'],
                'expires_at' => $validatedData['expires_at'],
            ]);

            return $this->apiResponse(true, 'Recompensa creada exitosamente.', $reward, null, 201);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->all();
            $firstError = $errors[0] ?? 'Error de validación de los datos.';
            return $this->apiResponse(false, $firstError, null, null, 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse(false, 'Error interno al crear la recompensa.', null, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'alliance_id' => 'sometimes|required|exists:alliances,id',
                'name' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string|max:255',
                'points_required' => 'sometimes|required|integer',
                'stock' => 'nullable|integer',
                'is_active' => 'sometimes|required|boolean',
                'expires_at' => 'nullable|date',
            ]);

            DB::beginTransaction();

            $reward = Reward::findOrFail($id);
            $reward->update($validatedData);

            DB::commit();
            return $this->apiResponse(true, 'Recompensa actualizada exitosamente.', $reward, null, 200);

        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Datos inválidos.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse(false, 'Error interno al actualizar la recompensa.', null, $e->getMessage(), 500);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $reward = Reward::findOrFail($id);
            $reward->delete();

            DB::commit();
            return $this->apiResponse(true, 'Recompensa eliminada exitosamente.', null, null, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse(false, 'Error al eliminar la recompensa.', null, $e->getMessage(), 500);
        }
    }
}
