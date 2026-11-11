<?php

namespace App\Http\Controllers;

use App\Http\Controllers\OldControllers\Controller;
use App\Http\Requests\Nearby\NearbyContainersRequest;
use App\Http\Requests\Nearby\NearbyRewardsRequest;
use App\Http\Requests\Nearby\NearbyShopsRequest;
use App\Http\Resources\NearbyContainerResource;
use App\Http\Resources\NearbyRewardResource;
use App\Http\Resources\NearbyShopResource;
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

    public function shops(NearbyShopsRequest $request)
    {
        $result = $this->nearby->shops($request->user(), $request->usableLocation(), $request->limit());

        return $this->apiResponse(true, 'Comercios obtenidos correctamente.', [
            'total_points' => $result['total_points'],
            'shops' => NearbyShopResource::collection($result['shops'])->resolve($request),
        ], null, 200);
    }

    public function containers(NearbyContainersRequest $request)
    {
        $containers = $this->nearby->containers($request->usableLocation(), $request->limit());

        return $this->apiResponse(true, 'Contenedores obtenidos correctamente.', [
            'containers' => NearbyContainerResource::collection($containers)->resolve($request),
        ], null, 200);
    }
}
