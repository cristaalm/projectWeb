<?php

namespace App\Http\Controllers;

use App\Exceptions\SystemLogException;
use App\Http\Controllers\OldControllers\Controller;
use App\Http\Requests\System\ShowSystemLogRequest;
use App\Services\SystemLogService;

class SystemLogController extends Controller
{
    public function __construct(
        private readonly SystemLogService $systemLogs,
    ) {}

    public function index()
    {
        return $this->apiResponse(true, 'Logs disponibles obtenidos correctamente.', [
            'logs' => $this->systemLogs->list(),
        ], null, 200);
    }

    public function show(ShowSystemLogRequest $request, string $source)
    {
        try {
            $data = $this->systemLogs->read(
                $source,
                (int) ($request->validated('lines') ?? SystemLogService::DEFAULT_LINES),
                $request->validated('search'),
            );
        } catch (SystemLogException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }

        return $this->apiResponse(true, 'Log obtenido correctamente.', $data, null, 200);
    }
}
