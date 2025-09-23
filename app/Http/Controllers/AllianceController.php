<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Alliance;
use Illuminate\Http\JsonResponse;
use App\Models\CatDepartments;
use Illuminate\Support\Facades\Storage;

class AllianceController extends Controller
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

            $allianceQuery = Alliance::query();

            if (!empty($query)) {
                $allianceQuery->where(function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('contact_name', 'like', '%' . $query . '%')
                    ->orWhere('contact_email', 'like', '%' . $query . '%')
                    ->orWhere('phone', 'like', '%' . $query . '%')
                    ->orWhere('address', 'like', '%' . $query . '%');
                });
            }

            if (in_array($status, [0, 1])) {
                $allianceQuery->where('status', $status);
            }

            $allowedKeys = ['name', 'status'];
            if (in_array($key, $allowedKeys) && in_array($order, ['asc', 'desc'])) {
                $allianceQuery->orderBy($key, $order);
            }

            $alliances = $allianceQuery->paginate($perPage);
            $data = $this->unsetDataPagination($alliances);
            return $this->apiResponse(true, 'Alianzas obtenidas exitosamente.', $data, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener las alianzas.', null, $e->getMessage(), 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'contact_name' => 'required|string|max:255',
                'contact_email' => 'required|email|max:255',
                'phone' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'status' => 'required|boolean',
            ]);

            $alliance = Alliance::create($validatedData);

            return $this->apiResponse(true, 'Alianza creada exitosamente.', $alliance, null, 201);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Error al crear la alianza.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al crear la alianza.', null, $e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id) {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'contact_name' => 'required|string|max:255',
                'contact_email' => 'required|email|max:255',
                'phone' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'status' => 'required|boolean',
            ]);

            $alliance = Alliance::findOrFail($id);
            $alliance->update($validatedData);

            return $this->apiResponse(true, 'Alianza actualizada exitosamente.', $alliance, null, 200);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Error al actualizar la alianza.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al actualizar la alianza.', null, $e->getMessage(), 500);
        }
    }

    public function delete(Request $request, $id)
    {
        try {

            $alliance = Alliance::find($id);

            if (!$alliance) {
                return $this->apiResponse(false, 'Alianza no encontrada.', null, null, 404);
            }

            // guardamos una copia de la alianza
            $allianceCopy = $alliance;

            try {

                $alliance->delete();
            } catch (\Illuminate\Database\QueryException $e) {
                if ($e->getCode() === '23503') {
                    return $this->apiResponse(false, 'No se puede eliminar el comercio, por que ya esta relacionado con otros elementos.', null, null, 422);
                }
                throw $e;
            }

            // una vez eliminada la alianza, eliminamos el logo
            $logoPath = "alliances/{$id}";
            if ($allianceCopy->logo && $allianceCopy->ext) {
                $oldFilePath = "{$logoPath}/logo.{$allianceCopy->ext}";
                if (Storage::disk('public')->exists($oldFilePath)) {
                    Storage::disk('public')->delete($oldFilePath);
                }
            }


            return $this->apiResponse(true, 'Comercio eliminado exitosamente.', null, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al eliminar el comercio.', null, $e->getMessage(), 500);
        }
    }

    public function updateLogo(Request $request, $id)
    {
        try {
            $alliance = Alliance::findOrFail($id);
    
            $validatedData = $request->validate([
                'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ]);
    
            $logoPath = "alliances/{$id}";
    
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $extension = $logo->getClientOriginalExtension();
                $filename = "logo.{$extension}";
    
                if ($alliance->logo && $alliance->ext) {
                    $oldFilePath = "{$logoPath}/logo.{$alliance->ext}";
                    if (Storage::disk('public')->exists($oldFilePath)) {
                        Storage::disk('public')->delete($oldFilePath);
                    }
                }
    
                Storage::disk('public')->putFileAs($logoPath, $logo, $filename);
    
                $alliance->logo = true;
                $alliance->ext = $extension;
    
            } else {
                if ($alliance->logo && $alliance->ext) {
                    $oldFilePath = "{$logoPath}/logo.{$alliance->ext}";
                    if (Storage::disk('public')->exists($oldFilePath)) {
                        Storage::disk('public')->delete($oldFilePath);
                    }
                }
    
                $alliance->logo = false;
                $alliance->ext = null;
            }
    
            $alliance->save();
    
            return $this->apiResponse(true, 'Logo actualizado exitosamente.', $alliance, null, 200);
    
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al actualizar el logo.', null, $e->getMessage(), 500);
        }
    }
}
