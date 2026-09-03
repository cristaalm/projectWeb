<?php

namespace App\Services;

use App\Exceptions\TypeShopException;
use App\Models\TypeShop;
use App\Repositories\TypeShopRepository;
use Illuminate\Database\QueryException;

class TypeShopService
{
    public function __construct(
        private readonly TypeShopRepository $typeShops,
    ) {
    }

    public function create(array $data): TypeShop
    {
        if ($this->typeShops->nameExists($data['name'])) {
            throw new TypeShopException('Ya existe una categoría con ese nombre.', 422);
        }

        return TypeShop::create($data);
    }

    public function update(TypeShop $typeShop, array $data): TypeShop
    {
        if ($this->typeShops->nameExists($data['name'], $typeShop->id)) {
            throw new TypeShopException('Ya existe una categoría con ese nombre.', 422);
        }

        $typeShop->update($data);

        return $typeShop;
    }

    public function delete(TypeShop $typeShop): void
    {
        try {
            $typeShop->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23503') {
                throw new TypeShopException(
                    'No se puede eliminar la categoría porque tiene alianzas vinculadas.',
                    422
                );
            }
            throw $e;
        }
    }
}
