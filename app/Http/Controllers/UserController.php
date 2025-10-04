<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Support\Facades\Log;

// notifications
use App\Notifications\UserStatusAccountNotification;

class UserController extends Controller
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

            $userQuery = User::query();

            if (!empty($query)) {
                $userQuery->where(function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                      ->orWhere('last_name', 'like', '%' . $query . '%')
                      ->orWhere('email', 'like', '%' . $query . '%')
                      ->orWhere('phone', 'like', '%' . $query . '%')
                      ->orWhere('total_points', 'like', '%' . $query . '%')
                      ->orWhereHas('role', function ($subQ) use ($query) {
                          $subQ->where('name', 'like', '%' . $query . '%');
                      });
                });
            }

            if (in_array($status, [0, 1])) {
                $userQuery->where('users.status', $status);
            }

            $allowedKeys = ['name', 'last_name', 'email', 'phone', 'total_points', 'created_at'];

            if (in_array($key, $allowedKeys) && in_array($order, ['asc', 'desc'])) {
                $userQuery->orderBy($key, $order);
            }

            $users = $userQuery->with('role')->paginate($perPage);
            $data = $this->unsetDataPagination($users);
            return $this->apiResponse(true, 'Usuarios obtenidos exitosamente.', $data, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener los usuarios.', null, $e->getMessage(), 500);
        }
    }

    public function toggleStatusAccount(Request $request)
    {
        try {
            $authUser = $request->user();

            $validateData = $request->validate([
                'id' => 'required|exists:users,id',
                'status' => 'required|boolean',
                'justification' => 'nullable|string',
            ]);

            $user = User::findOrFail($validateData['id']);
            $user->status = $validateData['status'];
            $user->save();
            
            $description = $validateData['status'] ? 'activo' : 'desactivo';
            try {
                $user->notify(new UserStatusAccountNotification($validateData['status'], $validateData['justification']));
            } catch (\Exception $e) {
                return $this->apiResponse(true, 'Se ' . $description . ' la cuenta, pero no se pudo enviar notificación al usuario.', null, $e->getMessage(), 200);
            }

            Log::info($authUser->name . ' toggleStatusAccount to ' . $user->name . ' ' . $description);

            return $this->apiResponse(true, 'La cuenta de ' . $user->name . ' se ' . $description . ' exitosamente.', null, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al actualizar el estado de la cuenta.', null, $e->getMessage(), 500);
        }
    }
}
