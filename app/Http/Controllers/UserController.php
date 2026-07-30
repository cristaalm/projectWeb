<?php

namespace App\Http\Controllers;

use App\Exceptions\UserManagementException;
use App\Http\Controllers\OldControllers\Controller;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\DeactivateUserRequest;
use App\Http\Requests\Users\DisableTwoFactorRequest;
use App\Http\Requests\Users\ListUsersRequest;
use App\Http\Requests\Users\ModifyPointsRequest;
use App\Http\Requests\Users\ResetCredentialsRequest;
use App\Http\Requests\Users\RestoreUserRequest;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use App\Services\UserManagementService;

class UserController extends Controller
{
    public function __construct(
        private readonly UserManagementService $userManagementService,
        private readonly UserRepository $users,
    ) {
    }

    public function index(ListUsersRequest $request)
    {
        $paginated = $this->users->paginate($request->validated());

        $data = $this->unsetDataPagination($paginated);
        $data['data'] = UserResource::collection($paginated->items());

        return $this->apiResponse(true, 'Usuarios obtenidos correctamente.', $data, null, 200);
    }

    public function store(CreateUserRequest $request)
    {
        $user = $this->userManagementService->createUser($request->validated());

        return $this->apiResponse(true, 'Usuario creado correctamente.', [
            'user' => new UserResource($user),
        ], null, 201);
    }

    public function modifyPoints(ModifyPointsRequest $request, int $userId)
    {
        $target = $this->users->findById($userId);

        if (! $target) {
            return $this->apiResponse(false, 'Usuario no encontrado.', null, null, 404);
        }

        try {
            $updated = $this->userManagementService->modifyPoints(
                $target,
                $request->user(),
                $request->validated('points'),
                $request->validated('reason'),
            );

            return $this->apiResponse(true, 'Puntos actualizados correctamente.', [
                'user' => new UserResource($updated),
            ], null, 200);
        } catch (UserManagementException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function deactivate(DeactivateUserRequest $request, int $userId)
    {
        $target = $this->users->findById($userId);

        if (! $target) {
            return $this->apiResponse(false, 'Usuario no encontrado.', null, null, 404);
        }

        try {
            $this->userManagementService->deactivate($target, $request->user(), $request->validated('reason'));

            return $this->apiResponse(true, 'Usuario dado de baja correctamente.', null, null, 200);
        } catch (UserManagementException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function restore(RestoreUserRequest $request, int $userId)
    {
        $target = $this->users->findById($userId, withTrashed: true);

        if (! $target) {
            return $this->apiResponse(false, 'Usuario no encontrado.', null, null, 404);
        }

        try {
            $restored = $this->userManagementService->restore($target, $request->user(), $request->validated('reason'));

            return $this->apiResponse(true, 'Usuario restaurado correctamente.', [
                'user' => new UserResource($restored),
            ], null, 200);
        } catch (UserManagementException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function resetCredentials(ResetCredentialsRequest $request, int $userId)
    {
        $target = $this->users->findById($userId);

        if (! $target) {
            return $this->apiResponse(false, 'Usuario no encontrado.', null, null, 404);
        }

        try {
            $this->userManagementService->resetCredentials($target, $request->user());

            return $this->apiResponse(true, 'Credenciales restablecidas correctamente.', null, null, 200);
        } catch (UserManagementException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function disableTwoFactor(DisableTwoFactorRequest $request, int $userId)
    {
        $target = $this->users->findById($userId);

        if (! $target) {
            return $this->apiResponse(false, 'Usuario no encontrado.', null, null, 404);
        }

        try {
            $updated = $this->userManagementService->disableTwoFactor($target, $request->user());

            return $this->apiResponse(true, '2FA deshabilitado correctamente.', [
                'user' => new UserResource($updated),
            ], null, 200);
        } catch (UserManagementException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }
}
