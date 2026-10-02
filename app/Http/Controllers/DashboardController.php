<?php

namespace App\Http\Controllers;

use App\Http\Controllers\OldControllers\Controller;
use App\Http\Requests\Dashboard\SuperadminDashboardRequest;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {}

    public function superadmin(SuperadminDashboardRequest $request)
    {
        $data = $this->dashboardService->superadminOverview(
            $request->validated('period') ?? DashboardService::DEFAULT_PERIOD,
            $request,
        );

        return $this->apiResponse(true, 'Panel obtenido correctamente.', $data, null, 200);
    }
}
