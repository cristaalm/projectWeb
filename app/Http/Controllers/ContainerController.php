<?php

namespace App\Http\Controllers;

use App\Exceptions\ContainerException;
use App\Http\Controllers\OldControllers\Controller;
use App\Http\Requests\Containers\CreateContainerRequest;
use App\Http\Requests\Containers\ListContainersRequest;
use App\Http\Requests\Containers\UpdateContainerRequest;
use App\Repositories\ContainerRepository;
use App\Services\ContainerService;

class ContainerController extends Controller
{
    public function __construct(
        private readonly ContainerService $containerService,
        private readonly ContainerRepository $containers,
    ) {
    }

    public function index(ListContainersRequest $request)
    {
        $paginated = $this->containers->paginate($request->validated());

        $data = $this->unsetDataPagination($paginated);
        $data['data'] = $paginated->items();

        return $this->apiResponse(true, 'Contenedores obtenidos correctamente.', $data, null, 200);
    }

    public function store(CreateContainerRequest $request)
    {
        try {
            $container = $this->containerService->create($request->validated());

            return $this->apiResponse(true, 'Contenedor creado correctamente.', [
                'container' => $container,
            ], null, 201);
        } catch (ContainerException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function update(UpdateContainerRequest $request, int $id)
    {
        $container = $this->containers->findById($id);

        if (! $container) {
            return $this->apiResponse(false, 'Contenedor no encontrado.', null, null, 404);
        }

        try {
            $updated = $this->containerService->update($container, $request->validated());

            return $this->apiResponse(true, 'Contenedor actualizado correctamente.', [
                'container' => $updated,
            ], null, 200);
        } catch (ContainerException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function destroy(int $id)
    {
        $container = $this->containers->findById($id);

        if (! $container) {
            return $this->apiResponse(false, 'Contenedor no encontrado.', null, null, 404);
        }

        $this->containerService->delete($container);

        return $this->apiResponse(true, 'Contenedor eliminado correctamente.', null, null, 200);
    }
}
