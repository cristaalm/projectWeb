<?php

namespace App\Http\Controllers;

use App\Http\Controllers\OldControllers\Controller;
use App\Http\Requests\Points\ListPointMovementsRequest;
use App\Http\Resources\PointMovementResource;
use App\Repositories\PointRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class PointController extends Controller
{
    public function __construct(
        private readonly PointRepository $points,
        private readonly UserRepository $users,
    ) {}

    public function balance(Request $request)
    {
        $userId = $request->user()->id;

        return $this->apiResponse(true, 'Saldo de puntos obtenido correctamente.', [
            'total_points' => $this->users->pointsBalance($userId),
            'last_changed_at' => $this->points->lastChangedAt($userId)?->toJSON(),
        ], null, 200);
    }

    public function movements(ListPointMovementsRequest $request)
    {
        $paginated = $this->points->movements($request->user()->id, $request->validated('per_page') ?? 15);

        $data = $this->unsetDataPagination($paginated);
        $data['data'] = PointMovementResource::collection($paginated->items())->resolve($request);

        return $this->apiResponse(true, 'Movimientos de puntos obtenidos correctamente.', $data, null, 200);
    }

    public function month(Request $request)
    {
        $month = now();

        return $this->apiResponse(true, 'Puntos del mes obtenidos correctamente.', [
            'month' => $month->format('Y-m'),
            'points_month' => $this->points->earnedInMonth($request->user()->id, $month),
        ], null, 200);
    }
}
