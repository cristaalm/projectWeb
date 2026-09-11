<?php

namespace App\Http\Controllers;

use App\Exceptions\RewardException;
use App\Http\Controllers\OldControllers\Controller;
use App\Http\Requests\Rewards\CreateRewardRequest;
use App\Http\Requests\Rewards\ListRewardsRequest;
use App\Http\Requests\Rewards\RejectRewardRequest;
use App\Http\Requests\Rewards\UpdateRewardImageRequest;
use App\Http\Requests\Rewards\UpdateRewardRequest;
use App\Repositories\RewardRepository;
use App\Services\RewardService;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function __construct(
        private readonly RewardService $rewardService,
        private readonly RewardRepository $rewards,
    ) {}

    public function index(ListRewardsRequest $request)
    {
        try {
            $filters = $this->rewardService->scopeAllianceFilter($request->validated(), $request->user());
        } catch (RewardException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }

        $paginated = $this->rewards->paginate($filters);

        $data = $this->unsetDataPagination($paginated);
        $data['data'] = $paginated->items();

        return $this->apiResponse(true, 'Recompensas obtenidas correctamente.', $data, null, 200);
    }

    public function store(CreateRewardRequest $request)
    {
        try {
            $reward = $this->rewardService->create($request->validated(), $request->user());

            return $this->apiResponse(true, 'Recompensa creada correctamente.', [
                'reward' => $reward,
            ], null, 201);
        } catch (RewardException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function update(UpdateRewardRequest $request, int $id)
    {
        $reward = $this->rewards->findById($id);

        if (! $reward) {
            return $this->apiResponse(false, 'Recompensa no encontrada.', null, null, 404);
        }

        try {
            $updated = $this->rewardService->update($reward, $request->validated(), $request->user());

            return $this->apiResponse(true, 'Recompensa actualizada correctamente.', [
                'reward' => $updated,
            ], null, 200);
        } catch (RewardException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function destroy(Request $request, int $id)
    {
        $reward = $this->rewards->findById($id);

        if (! $reward) {
            return $this->apiResponse(false, 'Recompensa no encontrada.', null, null, 404);
        }

        try {
            $this->rewardService->delete($reward, $request->user());

            return $this->apiResponse(true, 'Recompensa eliminada correctamente.', null, null, 200);
        } catch (RewardException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function uploadImage(UpdateRewardImageRequest $request, int $id)
    {
        $reward = $this->rewards->findById($id);

        if (! $reward) {
            return $this->apiResponse(false, 'Recompensa no encontrada.', null, null, 404);
        }

        try {
            $updated = $this->rewardService->updateImage($reward, $request->user(), $request->file('image'));

            return $this->apiResponse(true, 'Imagen actualizada correctamente.', [
                'reward' => $updated,
            ], null, 200);
        } catch (RewardException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function deleteImage(Request $request, int $id)
    {
        $reward = $this->rewards->findById($id);

        if (! $reward) {
            return $this->apiResponse(false, 'Recompensa no encontrada.', null, null, 404);
        }

        try {
            $updated = $this->rewardService->deleteImage($reward, $request->user());

            return $this->apiResponse(true, 'Imagen eliminada correctamente.', [
                'reward' => $updated,
            ], null, 200);
        } catch (RewardException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function approve(Request $request, int $id)
    {
        $reward = $this->rewards->findById($id);

        if (! $reward) {
            return $this->apiResponse(false, 'Recompensa no encontrada.', null, null, 404);
        }

        try {
            $updated = $this->rewardService->approve($reward, $request->user());

            return $this->apiResponse(true, 'Recompensa aprobada correctamente.', [
                'reward' => $updated,
            ], null, 200);
        } catch (RewardException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function reject(RejectRewardRequest $request, int $id)
    {
        $reward = $this->rewards->findById($id);

        if (! $reward) {
            return $this->apiResponse(false, 'Recompensa no encontrada.', null, null, 404);
        }

        try {
            $updated = $this->rewardService->reject($reward, $request->user(), $request->validated('reason'));

            return $this->apiResponse(true, 'Recompensa rechazada correctamente.', [
                'reward' => $updated,
            ], null, 200);
        } catch (RewardException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function pause(Request $request, int $id)
    {
        $reward = $this->rewards->findById($id);

        if (! $reward) {
            return $this->apiResponse(false, 'Recompensa no encontrada.', null, null, 404);
        }

        try {
            $updated = $this->rewardService->pause($reward, $request->user());

            return $this->apiResponse(true, 'Recompensa pausada correctamente.', [
                'reward' => $updated,
            ], null, 200);
        } catch (RewardException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function reactivate(Request $request, int $id)
    {
        $reward = $this->rewards->findById($id);

        if (! $reward) {
            return $this->apiResponse(false, 'Recompensa no encontrada.', null, null, 404);
        }

        try {
            $updated = $this->rewardService->reactivate($reward, $request->user());

            return $this->apiResponse(true, 'Recompensa reactivada correctamente.', [
                'reward' => $updated,
            ], null, 200);
        } catch (RewardException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }
}
