<?php

namespace App\Http\Controllers;

use App\Http\Controllers\OldControllers\Controller;
use App\Http\Requests\Nearby\NearbyRewardsRequest;
use App\Http\Resources\NearbyRewardResource;
use App\Services\NearbyService;

/**
 * Endpoints "cerca de ti" de la app móvil. Reciben la ubicación en el cuerpo de
 * un POST para que no quede en los logs de acceso.
 */
class NearbyController extends Controller
{
    public function __construct(
        private readonly NearbyService $nearby,
    ) {}

    public function rewards(NearbyRewardsRequest $request)
    {
        $result = $this->nearby->rewards($request->user(), $request->usableLocation(), $request->limit());

        return $this->apiResponse(true, 'Recompensas obtenidas correctamente.', [
            'location_used' => $result['location_used'],
            'total_points' => $result['total_points'],
            'rewards' => NearbyRewardResource::collection($result['rewards'])->resolve($request),
        ], null, 200);
    }
}
