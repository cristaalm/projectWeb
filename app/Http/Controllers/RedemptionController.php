<?php

namespace App\Http\Controllers;

use App\Exceptions\RedemptionException;
use App\Http\Controllers\OldControllers\Controller;
use App\Http\Requests\Redemptions\ListRedemptionsRequest;
use App\Repositories\PointRedemptionRepository;
use App\Services\RedemptionService;
use Illuminate\Http\Request;

class RedemptionController extends Controller
{
    public function __construct(
        private readonly RedemptionService $redemptionService,
        private readonly PointRedemptionRepository $redemptions,
    ) {}

    public function index(ListRedemptionsRequest $request)
    {
        try {
            $filters = $this->redemptionService->scopeAllianceFilter($request->validated(), $request->user());
        } catch (RedemptionException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }

        $paginated = $this->redemptions->paginate($filters);

        $data = $this->unsetDataPagination($paginated);
        $data['data'] = $paginated->items();

        return $this->apiResponse(true, 'Canjes obtenidos correctamente.', $data, null, 200);
    }

    public function deliver(Request $request, int $id)
    {
        $redemption = $this->redemptions->findById($id);

        if (! $redemption) {
            return $this->apiResponse(false, 'Canje no encontrado.', null, null, 404);
        }

        try {
            $updated = $this->redemptionService->markDelivered($redemption, $request->user());

            return $this->apiResponse(true, 'Canje marcado como entregado correctamente.', [
                'redemption' => $updated,
            ], null, 200);
        } catch (RedemptionException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }
}
