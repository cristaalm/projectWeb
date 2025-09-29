<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Alliance;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\TypeShop;

class TypeShopController extends Controller
{
    public function getAll(Request $request)
    {
        try {
            $perPage = (int) $request->input('per_page', 10);
            $perPage = max(1, min($perPage, 100));
            $query = $request->input('query', '');
            $key = $request->input('key');
            $order = strtolower($request->input('order', 'asc'));

            $typeShopQuery = TypeShop::query();

            if (!empty($query)) {
                $typeShopQuery->where(function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%');
                });
            }

            $allowedKeys = ['name'];
            if (in_array($key, $allowedKeys) && in_array($order, ['asc', 'desc'])) {
                $typeShopQuery->orderBy($key, $order);
            }

            $typeShops = $typeShopQuery->paginate($perPage);
            $data = $this->unsetDataPagination($typeShops);
            return $this->apiResponse(true, 'Categorias obtenidas exitosamente.', $data, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener las categorias.', null, $e->getMessage(), 500);
        }
    }

    public function catalog(Request $request)
    {
        try {

            $typeShops = TypeShop::select('id', 'name')->get();
            
            return $this->apiResponse(true, 'Categorias obtenidas exitosamente.', $typeShops, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener las categorias.', null, $e->getMessage(), 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $typeShopExists = TypeShop::where('name', $validatedData['name'])->exists();
            if ($typeShopExists) {
                return $this->apiResponse(false, 'Ya existe una categoria con el mismo nombre.', null, null, 422);
            }

            $typeShop = TypeShop::create($validatedData);

            return $this->apiResponse(true, 'Categoria creado exitosamente.', $typeShop, null, 201);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Error al crear la categoria.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al crear la categoria.', null, $e->getMessage(), 500);
        }
    }

    public function delete(Request $request, $id)
    {
        try {

            $typeShop = TypeShop::find($id);

            if (!$typeShop) {
                return $this->apiResponse(false, 'Categoria no encontrada.', null, null, 404);
            }

            try {
                $typeShop->delete();
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() === '23503') {
                    return $this->apiResponse(false, 'No se puede eliminar la categoria, por que ya esta relacionado con otros elementos.', null, null, 422);
                }
                throw $e;
            }

            return $this->apiResponse(true, 'Categoria eliminada exitosamente.', null, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al eliminar la categoria.', null, $e->getMessage(), 500);
        }
    }
}
