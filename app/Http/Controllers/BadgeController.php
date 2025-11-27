<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BadgeController extends Controller
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
            
            $key = $normalize($request->input('key')) ?? 'updated_at';
            $order = strtolower($normalize($request->input('order')) ?? 'desc');
            $perPage = (int) ($normalize($request->input('per_page')) ?? 10);
            $perPage = max(1, min($perPage, 100));
            $query = $normalize($request->input('query')) ?? '';
            $status = $normalize($request->input('status'));

            $badgeQuery = Badge::query();

            if (!empty($query)) {
                $badgeQuery->where(function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('points_required', 'like', '%' . $query . '%')
                    ->orWhere('points_awared', 'like', '%' . $query . '%');
                });
            }

            if (in_array($status, [0, 1], true)) {
                $badgeQuery->where('status', $status);
            }

            $allowedKeys = ['name', 'points_required', 'points_awared', 'updated_at'];

            $validKey = $key !== null && in_array($key, $allowedKeys);
            $validOrder = $order !== null && in_array(strtolower($order), ['asc', 'desc']);

            if ($validKey && $validOrder) {
                $order = strtolower($order);
                $badgeQuery->orderBy($key, $order);
            }

            $badges = $badgeQuery->paginate($perPage);
            $data = $this->unsetDataPagination($badges);
            return $this->apiResponse(true, 'Badges obtenidos exitosamente.', $data, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener los comercios.', null, $e->getMessage(), 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'points_required' => 'required|integer',
                'points_awared' => 'required|integer',
                'status' => 'required|boolean',
            ]);

            $badge = Badge::create($validatedData);

            return $this->apiResponse(true, 'Badge creado exitosamente.', $badge, null, 201);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Error al crear el badge.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al crear el badge.', null, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'points_required' => 'required|integer',
                'points_awared' => 'required|integer',
                'status' => 'required|boolean',
            ]);

            $badge = Badge::findOrFail($id);
            $badge->update($validatedData);

            return $this->apiResponse(true, 'Badge actualizado exitosamente.', $badge, null, 200);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Error al actualizar el badge.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al actualizar el badge.', null, $e->getMessage(), 500);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $badge = Badge::findOrFail($id);
            $badge->delete();

            return $this->apiResponse(true, 'Badge eliminado exitosamente.', null, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al eliminar el badge.', null, $e->getMessage(), 500);
        }
    }

    public function claimBadge(Request $request)
    {
        try {
            $validateData = $request->validate([
                'user_id' => 'required|integer|exists:users,id',
                'badge' => 'required|integer|exists:badge,id',
            ]);
    
            $user = User::findOrFail($validateData['user_id']);
            $badgeId = $validateData['badge'];

            $badge = Badge::where('id', $badgeId)->where('status', true)->first();
    
            if ($user->points_month < $badge->points_required) {
                return $this->apiResponse(false, "No cumples con los puntos necesarios para obtener la insignia '$badge->name'. Necesitas al menos $badge->points_required puntos este mes.", null, null, 403);
            }
    
            $badges = $user->badge ?? [];

            if (in_array($badgeId, $badges)) {
                return $this->apiResponse(true, "Ya tienes la insignia '$badge->name'.", null, null, 200);
            }

            $badges[] = $badgeId;

            $user->badge = $badges;
            // sumamos los puntos de la recompenza+
            $user->total_points = $user->total_points + $badge->points_awared;
            $user->save();
    
            return $this->apiResponse(true, "¡Felicidades! Has obtenido la insignia '$badge->name'.", $badges, null, 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->apiResponse(false, 'Datos inválidos.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Ocurrió un error al intentar actualizar la insignia del usuario.', null, $e->getMessage(), 500);
        }
    }

    public function catalogBadges(Request $request)
    {
        try {
            $badges = Badge::select('id', 'name', 'points_required', 'points_awared')->get();
            return $this->apiResponse(true, 'Badges obtenidos exitosamente.', $badges, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener los badges.', null, $e->getMessage(), 500);
        }
    }
}
