<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ContainerController extends Controller
{
    public function getAll(Request $request)
    {
        try {
            $perPage = (int) $request->input('per_page', 10);
            $perPage = max(1, min($perPage, 100));
            $query = $request->input('query', '');
            $key = $request->input('key');
            $order = strtolower($request->input('order', 'asc'));
            $status = $request->input('status');

            $containerQuery = Container::query();

            if (!empty($query)) {
                $containerQuery->where(function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                      ->orWhere('serial_number', 'like', '%' . $query . '%')
                      ->orWhere('location', 'like', '%' . $query . '%');
                });
            }

            if (in_array($status, [0, 1])) {
                $containerQuery->where('status', $status);
            }

            $allowedKeys = ['serial_number', 'location', 'status'];

            if (in_array($key, $allowedKeys) && in_array($order, ['asc', 'desc'])) {
                $containerQuery->orderBy($key, $order);
            }

            $containers = $containerQuery->paginate($perPage);
            $data = $this->unsetDataPagination($containers);
            return $this->apiResponse(true, 'Contenedores obtenidos exitosamente.', $data, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener los comercios.', null, $e->getMessage(), 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'serial_number' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'status' => 'required|boolean',
            ]);

            $container = Container::create($validatedData);

            return $this->apiResponse(true, 'Comercio creado exitosamente.', $container, null, 201);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Error al crear el comercio.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al crear el comercio.', null, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id) 
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'serial_number' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'status' => 'required|boolean',
            ]);

            $container = Container::find($id);

            if (!$container) {
                return $this->apiResponse(false, 'Comercio seleccionado no existe.', null, null, 404);
            }

            $container->update($validatedData);

            return $this->apiResponse(true, 'Comercio actualizado exitosamente.', $container, null, 200);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Error al actualizar el comercio.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al actualizar el comercio.', null, $e->getMessage(), 500);
        }
    }

    public function delete(Request $request, $id)
    {
        try {

            $container = Container::find($id);

            if (!$container) {
                return $this->apiResponse(false, 'Comercio seleccionado no existe.', null, null, 404);
            }

            try {

                $container->delete();
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() === '23503') {
                    return $this->apiResponse(false, 'No se puede eliminar el comercio, por que ya esta relacionado con otros elementos.', null, null, 422);
                }
                throw $e;
            }

            return $this->apiResponse(true, 'Comercio eliminado exitosamente.', null, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al eliminar el comercio.', null, $e->getMessage(), 500);
        }
    }

    public function catalog(Request $request) {
        try {
            $alliances = Container::select('id', 'name', 'location')->where('status', 1)->get();
            return $this->apiResponse(true, 'Catalogo obtenido exitosamente.', $alliances, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener el catalogo.', null, $e->getMessage(), 500);
        }
    }
}
