<?php

namespace App\Http\Controllers;

use App\Exceptions\TypeShopException;
use App\Http\Controllers\OldControllers\Controller;
use App\Http\Requests\TypeShop\CreateTypeShopRequest;
use App\Http\Requests\TypeShop\ListTypeShopRequest;
use App\Http\Requests\TypeShop\UpdateTypeShopRequest;
use App\Models\TypeShop;
use App\Repositories\TypeShopRepository;
use App\Services\TypeShopService;

class TypeShopController extends Controller
{
    public function __construct(
        private readonly TypeShopService $typeShopService,
        private readonly TypeShopRepository $typeShops,
    ) {
    }

    public function catalog()
    {
        $typeShops = TypeShop::orderBy('name')->get(['id', 'name', 'is_active']);

        return $this->apiResponse(true, 'Categorías obtenidas correctamente.', [
            'type_shops' => $typeShops,
        ], null, 200);
    }

    public function index(ListTypeShopRequest $request)
    {
        $paginated = $this->typeShops->paginate($request->validated());

        $data = $this->unsetDataPagination($paginated);
        $data['data'] = $paginated->items();

        return $this->apiResponse(true, 'Categorías obtenidas correctamente.', $data, null, 200);
    }

    public function store(CreateTypeShopRequest $request)
    {
        try {
            $typeShop = $this->typeShopService->create($request->validated());

            return $this->apiResponse(true, 'Categoría creada correctamente.', [
                'type_shop' => $typeShop,
            ], null, 201);
        } catch (TypeShopException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function update(UpdateTypeShopRequest $request, int $id)
    {
        $typeShop = $this->typeShops->findById($id);

        if (! $typeShop) {
            return $this->apiResponse(false, 'Categoría no encontrada.', null, null, 404);
        }

        try {
            $updated = $this->typeShopService->update($typeShop, $request->validated());

            return $this->apiResponse(true, 'Categoría actualizada correctamente.', [
                'type_shop' => $updated,
            ], null, 200);
        } catch (TypeShopException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function destroy(int $id)
    {
        $typeShop = $this->typeShops->findById($id);

        if (! $typeShop) {
            return $this->apiResponse(false, 'Categoría no encontrada.', null, null, 404);
        }

        try {
            $this->typeShopService->delete($typeShop);

            return $this->apiResponse(true, 'Categoría eliminada correctamente.', null, null, 200);
        } catch (TypeShopException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }
}
