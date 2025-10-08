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
            $perPage = (int) $request->input('per_page', 10);
            $perPage = max(1, min($perPage, 100));
            $query = $request->input('query', '');
            $key = $request->input('key');
            $order = strtolower($request->input('order', 'asc'));
            $status = $request->input('status');
            $allianceId = $request->input('alliance_id');

            $rewardQuery = Reward::query();

            if (!empty($query)) {
                $rewardQuery->where(function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                      ->orWhere('description', 'like', '%' . $query . '%')
                      ->orWhere('code', 'like', '%' . $query . '%')
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
            
            $allowedKeys = ['name', 'description', 'code', 'status', 'alliance.name'];
            
            if (in_array($key, $allowedKeys) && in_array($order, ['asc', 'desc'])) {
                if ($key === 'alliance.name') {
                    $rewardQuery->join('alliances', 'rewards.alliance_id', '=', 'alliances.id')
                    ->orderBy('alliances.name', $order);
                } else {
                    $rewardQuery->orderBy($key, $order);
                }
            }
            
            $rewards = $rewardQuery->paginate($perPage);
            return $this->apiResponse(true, 'Recompensas obtenidos exitosamente.', $rewards, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener las recompensas.', null, $e->getMessage(), 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'points_required' => 'required|integer',
                'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                'stock' => 'nullable|integer',
                'code' => 'required|string|max:255',
                'is_active' => 'required|boolean',
                'expires_at' => 'nullable|date',
                'alliance_id' => 'required|exists:alliances,id',
            ]);
    
            DB::beginTransaction();
    
            $reward = Reward::create(array_merge($validatedData, [
                'image' => false,
            ]));
    
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $ext = $file->extension();
                $filename = "{$reward->id}.{$ext}";
                $path = "rewards/{$reward->alliance_id}";
                $filepath = "$path/$filename";
    
                try {
                    Storage::disk('public')->putFileAs($path, $file, $filename);
                    $reward->update(['image' => true]);
                } catch (\Exception $e) {
                    if (Storage::disk('public')->exists($filepath)) {
                        Storage::disk('public')->delete($filepath);
                    }
                    DB::rollBack();
                    return $this->apiResponse(false, 'Error al subir la imagen.', null, $e->getMessage(), 500);
                }
            }
    
            DB::commit();
            return $this->apiResponse(true, 'Recompensa creada exitosamente.', $reward, null, 201);
    
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Datos inválidos.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse(false, 'Error interno al crear la recompensa.', null, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|required|string|max:255',
                'points_required' => 'sometimes|required|integer',
                'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                'stock' => 'nullable|integer',
                'code' => 'sometimes|required|string|max:255',
                'is_active' => 'sometimes|required|boolean',
                'expires_at' => 'nullable|date',
                'alliance_id' => 'sometimes|required|exists:alliances,id',
            ]);

            DB::beginTransaction();

            $reward = Reward::findOrFail($id);
            $reward->update($validatedData);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $ext = $file->extension();
                $filename = "{$reward->id}.{$ext}";
                $path = "rewards/{$reward->alliance_id}";
                $newFilepath = "$path/$filename";

                try {
                    if ($reward->image) {
                        $oldFilepath = "rewards/{$reward->alliance_id}/{$reward->id}.*";
                        $existingFiles = Storage::disk('public')->files("rewards/{$reward->alliance_id}");
                        foreach ($existingFiles as $file) {
                            if (basename($file) === "{$reward->id}." . pathinfo($file, PATHINFO_EXTENSION)) {
                                Storage::disk('public')->delete($file);
                                break;
                            }
                        }
                    }

                    Storage::disk('public')->putFileAs($path, $file, $filename);

                    $reward->update(['image' => true]);

                } catch (\Exception $e) {
                    DB::rollBack();
                    return $this->apiResponse(false, 'Error al actualizar la imagen.', null, $e->getMessage(), 500);
                }
            }

            DB::commit();
            return $this->apiResponse(true, 'Recompensa actualizada exitosamente.', $reward, null, 200);

        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Datos inválidos.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse(false, 'Error interno al actualizar la recompensa.', null, $e->getMessage(), 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $reward = Reward::findOrFail($id);

            if ($reward->image) {
                $allianceId = $reward->alliance_id;
                $rewardId = $reward->id;
                $imageDirectory = "rewards/{$allianceId}";
                $existingFiles = Storage::disk('public')->files($imageDirectory);

                foreach ($existingFiles as $file) {
                    $filename = basename($file);
                    if (preg_match("/^{$rewardId}\.\w+$/", $filename)) {
                        Storage::disk('public')->delete($file);
                        break;
                    }
                }
            }

            $reward->delete();

            DB::commit();

            return $this->apiResponse(true, 'Recompensa eliminada exitosamente.', null, null, 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse(false, 'Error al eliminar la recompensa.', null, $e->getMessage(), 500);
        }
    }
}
