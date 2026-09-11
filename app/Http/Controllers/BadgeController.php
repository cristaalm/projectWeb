<?php

namespace App\Http\Controllers;

use App\Exceptions\BadgeException;
use App\Http\Controllers\OldControllers\Controller;
use App\Http\Requests\Badges\ClaimBadgeRequest;
use App\Http\Requests\Badges\CreateBadgeRequest;
use App\Http\Requests\Badges\ListBadgesRequest;
use App\Http\Requests\Badges\UpdateBadgeRequest;
use App\Repositories\BadgeRepository;
use App\Services\BadgeService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function __construct(
        private readonly BadgeService $badgeService,
        private readonly BadgeRepository $badges,
    ) {}

    public function index(ListBadgesRequest $request)
    {
        $paginated = $this->badges->paginate($request->validated());

        $data = $this->unsetDataPagination($paginated);
        $data['data'] = $paginated->items();

        return $this->apiResponse(true, 'Insignias obtenidas correctamente.', $data, null, 200);
    }

    public function store(CreateBadgeRequest $request)
    {
        try {
            $badge = $this->badgeService->create($request->validated());

            return $this->apiResponse(true, 'Insignia creada correctamente.', [
                'badge' => $badge,
            ], null, 201);
        } catch (BadgeException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function update(UpdateBadgeRequest $request, int $id)
    {
        $badge = $this->badges->findById($id);

        if (! $badge) {
            return $this->apiResponse(false, 'Insignia no encontrada.', null, null, 404);
        }

        try {
            $updated = $this->badgeService->update($badge, $request->validated());

            return $this->apiResponse(true, 'Insignia actualizada correctamente.', [
                'badge' => $updated,
            ], null, 200);
        } catch (BadgeException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function destroy(int $id)
    {
        $badge = $this->badges->findById($id);

        if (! $badge) {
            return $this->apiResponse(false, 'Insignia no encontrada.', null, null, 404);
        }

        try {
            $this->badgeService->delete($badge);

            return $this->apiResponse(true, 'Insignia eliminada correctamente.', null, null, 200);
        } catch (BadgeException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function catalog()
    {
        return $this->apiResponse(true, 'Insignias obtenidas correctamente.', [
            'badges' => $this->badgeService->catalog(),
        ], null, 200);
    }

    public function myProgress(Request $request)
    {
        return $this->apiResponse(true, 'Progreso obtenido correctamente.', [
            'progress' => $this->badgeService->myProgress($request->user()),
        ], null, 200);
    }

    public function myHistory(Request $request)
    {
        return $this->apiResponse(true, 'Historial obtenido correctamente.', [
            'history' => $this->badgeService->myHistory($request->user()),
        ], null, 200);
    }

    public function pendingClaims(Request $request)
    {
        return $this->apiResponse(true, 'Reclamos pendientes obtenidos correctamente.', [
            'pending' => $this->badgeService->pendingClaims($request->user()),
        ], null, 200);
    }

    public function claim(ClaimBadgeRequest $request)
    {
        $badge = $this->badges->findById($request->validated('badge_id'));

        if (! $badge) {
            return $this->apiResponse(false, 'Insignia no encontrada.', null, null, 404);
        }

        try {
            $badgeUser = $this->badgeService->claim(
                $request->user(),
                $badge,
                Carbon::parse($request->validated('month'))->startOfMonth()
            );

            return $this->apiResponse(true, "¡Felicidades! Has reclamado la insignia '{$badge->name}'.", [
                'badge_user' => $badgeUser,
            ], null, 201);
        } catch (BadgeException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }
}
