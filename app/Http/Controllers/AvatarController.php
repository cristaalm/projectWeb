<?php

namespace App\Http\Controllers;

use App\Exceptions\AvatarException;
use App\Http\Controllers\OldControllers\Controller;
use App\Http\Requests\Avatar\IdentifyRequest;
use App\Http\Requests\Avatar\StoreFeedbackRequest;
use App\Http\Requests\Avatar\StoreMemoryRequest;
use App\Http\Requests\Avatar\UpdateAvatarStateRequest;
use App\Repositories\UserRepository;
use App\Services\AvatarService;

class AvatarController extends Controller
{
    public function __construct(
        private readonly AvatarService $avatarService,
        private readonly UserRepository $users,
    ) {}

    public function identify(IdentifyRequest $request)
    {
        try {
            $snapshot = $this->avatarService->identify(
                $request->validated('code_identity'),
                $request->validated('container_serial_number'),
            );

            return $this->apiResponse(true, 'Identificación exitosa.', $snapshot, null, 200);
        } catch (AvatarException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function storeMemory(StoreMemoryRequest $request, int $user)
    {
        if (! $this->users->findById($user)) {
            return $this->apiResponse(false, 'Usuario no encontrado.', null, null, 404);
        }

        $memory = $this->avatarService->storeMemory(
            $user,
            $request->validated('content'),
            $request->validated('metadata'),
        );

        return $this->apiResponse(true, 'Memoria guardada correctamente.', ['memory' => $memory], null, 201);
    }

    public function storeFeedback(StoreFeedbackRequest $request, int $user)
    {
        if (! $this->users->findById($user)) {
            return $this->apiResponse(false, 'Usuario no encontrado.', null, null, 404);
        }

        $feedback = $this->avatarService->storeFeedback(
            $user,
            $request->validated('thread_id'),
            $request->validated('rating'),
            $request->validated('comment'),
        );

        return $this->apiResponse(true, 'Valoración registrada correctamente.', ['feedback' => $feedback], null, 201);
    }

    public function updateState(UpdateAvatarStateRequest $request, int $user)
    {
        if (! $this->users->findById($user)) {
            return $this->apiResponse(false, 'Usuario no encontrado.', null, null, 404);
        }

        try {
            $avatar = $this->avatarService->updateAvatarState(
                $user,
                $request->validated('current_mood'),
                $request->validated('state'),
            );

            return $this->apiResponse(true, 'Estado del avatar actualizado correctamente.', ['avatar' => $avatar], null, 200);
        } catch (AvatarException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }
}
