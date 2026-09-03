<?php

namespace App\Http\Controllers;

use App\Enums\AllianceStatus;
use App\Exceptions\AllianceException;
use App\Http\Controllers\OldControllers\Controller;
use App\Http\Requests\Alliances\CreateAllianceRequest;
use App\Http\Requests\Alliances\ListAlliancesRequest;
use App\Http\Requests\Alliances\UpdateAllianceLogoRequest;
use App\Http\Requests\Alliances\UpdateAllianceRequest;
use App\Models\Alliance;
use App\Repositories\AllianceRepository;
use App\Services\AllianceService;

class AllianceController extends Controller
{
    public function __construct(
        private readonly AllianceService $allianceService,
        private readonly AllianceRepository $alliances,
    ) {
    }

    public function catalog()
    {
        $alliances = Alliance::where('status', AllianceStatus::ACTIVE->value)
            ->orderBy('name')
            ->get(['id', 'name', 'has_exclusive_rewards']);

        return $this->apiResponse(true, 'Alianzas obtenidas correctamente.', [
            'alliances' => $alliances,
        ], null, 200);
    }

    public function index(ListAlliancesRequest $request)
    {
        $paginated = $this->alliances->paginate($request->validated());

        $data = $this->unsetDataPagination($paginated);
        $data['data'] = $paginated->items();

        return $this->apiResponse(true, 'Alianzas obtenidas correctamente.', $data, null, 200);
    }

    public function store(CreateAllianceRequest $request)
    {
        try {
            $alliance = $this->allianceService->create($request->validated());

            return $this->apiResponse(true, 'Alianza creada correctamente.', [
                'alliance' => $alliance,
            ], null, 201);
        } catch (AllianceException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function update(UpdateAllianceRequest $request, int $id)
    {
        $alliance = $this->alliances->findById($id);

        if (! $alliance) {
            return $this->apiResponse(false, 'Alianza no encontrada.', null, null, 404);
        }

        try {
            $updated = $this->allianceService->update($alliance, $request->validated());

            return $this->apiResponse(true, 'Alianza actualizada correctamente.', [
                'alliance' => $updated,
            ], null, 200);
        } catch (AllianceException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function destroy(int $id)
    {
        $alliance = $this->alliances->findById($id);

        if (! $alliance) {
            return $this->apiResponse(false, 'Alianza no encontrada.', null, null, 404);
        }

        try {
            $this->allianceService->delete($alliance);

            return $this->apiResponse(true, 'Alianza eliminada correctamente.', null, null, 200);
        } catch (AllianceException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function uploadLogo(UpdateAllianceLogoRequest $request, int $id)
    {
        $alliance = $this->alliances->findById($id);

        if (! $alliance) {
            return $this->apiResponse(false, 'Alianza no encontrada.', null, null, 404);
        }

        $updated = $this->allianceService->updateLogo($alliance, $request->file('logo'));

        return $this->apiResponse(true, 'Logo actualizado correctamente.', [
            'alliance' => $updated,
        ], null, 200);
    }

    public function deleteLogo(int $id)
    {
        $alliance = $this->alliances->findById($id);

        if (! $alliance) {
            return $this->apiResponse(false, 'Alianza no encontrada.', null, null, 404);
        }

        $updated = $this->allianceService->deleteLogo($alliance);

        return $this->apiResponse(true, 'Logo eliminado correctamente.', [
            'alliance' => $updated,
        ], null, 200);
    }
}
