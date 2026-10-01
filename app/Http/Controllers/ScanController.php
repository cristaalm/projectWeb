<?php

namespace App\Http\Controllers;

use App\Enums\ScanStatus;
use App\Exceptions\ScanException;
use App\Http\Controllers\OldControllers\Controller;
use App\Http\Requests\Scans\ListScansRequest;
use App\Http\Requests\Scans\RegisterScanRequest;
use App\Http\Resources\ScanResource;
use App\Repositories\ScanRepository;
use App\Services\ScanService;

class ScanController extends Controller
{
    public function __construct(
        private readonly ScanService $scanService,
        private readonly ScanRepository $scans,
    ) {}

    public function index(ListScansRequest $request)
    {
        $paginated = $this->scans->paginate($request->validated());

        $data = $this->unsetDataPagination($paginated);
        $data['data'] = ScanResource::collection($paginated->items())->resolve($request);

        return $this->apiResponse(true, 'Escaneos obtenidos correctamente.', $data, null, 200);
    }

    public function store(RegisterScanRequest $request)
    {
        try {
            $result = $this->scanService->register($request->validated(), $request->file('image'));
        } catch (ScanException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }

        $scan = $result['scan'];
        $result['scan'] = (new ScanResource($scan))->resolve($request);

        if ($result['duplicate']) {
            return $this->apiResponse(true, 'Este escaneo ya había sido registrado.', $result, null, 200);
        }

        $message = $scan->scan_status === ScanStatus::SUCCESS
            ? 'Escaneo registrado correctamente.'
            : 'Escaneo registrado como no válido.';

        return $this->apiResponse(true, $message, $result, null, 201);
    }
}
