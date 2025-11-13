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
            $normalize = function ($value) {
                if (is_string($value) && strtolower($value) === 'null') {
                    return null;
                }
                return $value;
            };
    
            $perPage = (int) ($normalize($request->input('per_page')) ?? 10);
            $perPage = max(1, min($perPage, 100));
            $query = $normalize($request->input('query')) ?? '';
            $key = $normalize($request->input('key')) ?? 'updated_at';
            $order = strtolower($normalize($request->input('order')) ?? 'desc');
            $status = $normalize($request->input('status'));

            $containerQuery = Container::query();

            if (!empty($query)) {
                $containerQuery->where(function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                      ->orWhere('serial_number', 'like', '%' . $query . '%')
                      ->orWhere('location', 'like', '%' . $query . '%');
                });
            }

            if ($status != null && in_array($status, [0, 1])) {
                $containerQuery->where('status', $status);
            }

            $allowedKeys = ['serial_number', 'location', 'status', 'updated_at'];

            if ($key != null && in_array($key, $allowedKeys) && in_array($order, ['asc', 'desc'])) {
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

    public function catalog(Request $request) 
    {
        try {
            $alliances = Container::select('id', 'name', 'location')->where('status', 1)->get();
            return $this->apiResponse(true, 'Catalogo obtenido exitosamente.', $alliances, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener el catalogo.', null, $e->getMessage(), 500);
        }
    }
    
    public function updateCapacity(Request $request, int $id)
    {
        try {

            // hacemos merge del id
            $request->merge(['container_id' => $id]);

            $validatedData = $request->validate([
                'container_id' => 'required|exists:containers,id',
                'capacity' => 'required|array',
            ]);

            $container = Container::find($validatedData['container_id']);

            if (!$container) {
                return $this->apiResponse(false, 'Comercio seleccionado no existe.', null, null, 404);
            }

            // obtenemos la capacidad del contenedor
            $capacityContainer = $container->capacity;

            // actualizamos solo los sensores que vienen en el request
            if (isset($validatedData['capacity']['sensor1'])) $capacityContainer['sensor1'] = $validatedData['capacity']['sensor1'];
            if (isset($validatedData['capacity']['sensor2'])) $capacityContainer['sensor2'] = $validatedData['capacity']['sensor2'];
            if (isset($validatedData['capacity']['sensor3'])) $capacityContainer['sensor3'] = $validatedData['capacity']['sensor3'];

            $container->update(['capacity' => $capacityContainer]);

            return $this->apiResponse(true, 'Comercio actualizado exitosamente.', $container, null, 200);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Error al actualizar el comercio.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al actualizar el comercio.', null, $e->getMessage(), 500);
        }
    }
}
