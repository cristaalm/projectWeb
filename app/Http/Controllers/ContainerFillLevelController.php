<?php

namespace App\Http\Controllers;

use App\Enums\FillReadingSource;
use App\Http\Controllers\OldControllers\Controller;
use App\Http\Requests\Containers\ReportFillLevelsRequest;
use App\Services\ContainerFillLevelService;
use Illuminate\Support\Carbon;

/**
 * Reporte periódico del nivel de llenado que manda el contenedor físico,
 * autenticado con su propio token (middleware container.token).
 */
class ContainerFillLevelController extends Controller
{
    public function __construct(
        private readonly ContainerFillLevelService $fillLevels,
    ) {}

    public function store(ReportFillLevelsRequest $request)
    {
        // El contenedor lo resuelve el middleware a partir del token, nunca
        // el body de la petición.
        $container = $request->attributes->get('container');
        $measuredAt = $request->filled('measured_at')
            ? Carbon::parse($request->validated('measured_at'))
            : now();

        $sensors = $this->fillLevels->record(
            $container,
            $request->validated('sensors'),
            FillReadingSource::REPORT,
            $measuredAt,
        );

        return $this->apiResponse(true, 'Niveles de llenado registrados correctamente.', [
            'measured_at' => $measuredAt->toJSON(),
            'sensors' => $sensors,
        ], null, 200);
    }
}
