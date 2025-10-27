<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Alliance;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AllianceController extends Controller
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
            
            $key = $normalize($request->input('key')) ?? 'updated_at';
            $order = strtolower($normalize($request->input('order')) ?? 'desc');
            $perPage = (int) ($normalize($request->input('per_page')) ?? 10);
            $perPage = max(1, min($perPage, 100));
            $query = $normalize($request->input('query')) ?? '';
            $status = $normalize($request->input('status'));

            $allianceQuery = Alliance::query();

            if (!empty($query)) {
                $allianceQuery->where(function ($q) use ($query) {
                    $q->where('name', 'like', '%' . $query . '%')
                    ->orWhere('contact_name', 'like', '%' . $query . '%')
                    ->orWhere('contact_email', 'like', '%' . $query . '%')
                    ->orWhere('phone', 'like', '%' . $query . '%')
                    ->orWhere('address', 'like', '%' . $query . '%')
                    ->orWhereHas('typeShop', function ($subQ) use ($query) {
                        $subQ->where('name', 'like', '%' . $query . '%');
                    });
                });
            }

            if (in_array($status, [0, 1], true)) {
                $allianceQuery->where('status', $status);
            }

            $allowedKeys = ['name', 'contact_name', 'contact_email', 'phone', 'address', 'status', 'type_shop.name', 'updated_at'];

            $validKey = $key !== null && in_array($key, $allowedKeys);
            $validOrder = $order !== null && in_array(strtolower($order), ['asc', 'desc']);

            if ($validKey && $validOrder) {
                $order = strtolower($order);
                if ($key === 'type_shop.name') {
                    $allianceQuery->join('type_shop', 'alliances.type_shop_id', '=', 'type_shop.id')
                                ->orderBy('type_shop.name', $order);
                } else {
                    $allianceQuery->orderBy($key, $order);
                }
            }

            $alliances = $allianceQuery->with('typeShop')->paginate($perPage);
            $data = $this->unsetDataPagination($alliances);
            return $this->apiResponse(true, 'Comercios obtenidos exitosamente.', $data, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener los comercios.', null, $e->getMessage(), 500);
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
                'type_shop_id' => 'required|exists:type_shop,id',
                'status' => 'required|boolean',
            ]);

            $alliance = Alliance::create($validatedData);

            return $this->apiResponse(true, 'Comercio creado exitosamente.', $alliance, null, 201);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Error al crear el comercio.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al crear el comercio.', null, $e->getMessage(), 500);
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
                'type_shop_id' => 'required|exists:type_shop,id',
                'status' => 'required|boolean',
            ]);

            $alliance = Alliance::findOrFail($id);
            $alliance->update($validatedData);

            return $this->apiResponse(true, 'Comercio actualizado exitosamente.', $alliance, null, 200);
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Error al actualizar el comercio.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al actualizar el comercio.', null, $e->getMessage(), 500);
        }
    }

    public function delete(Request $request, $id)
    {
        try {

            $alliance = Alliance::find($id);

            if (!$alliance) {
                return $this->apiResponse(false, 'Comercio no encontrado.', null, null, 404);
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

    public function catalog(Request $request) {
        try {
            $alliances = Alliance::select('id', 'name')->where('status', 1)->get();
            return $this->apiResponse(true, 'Catalogo obtenido exitosamente.', $alliances, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener el catalogo.', null, $e->getMessage(), 500);
        }
    }

    public function cashCut(Request $request, int $alliance_id) 
    {
        try {
            $request->merge([
                'alliance_id' => $alliance_id,
            ]);

            $validateData = $request->validate([
                'alliance_id' => 'required|exists:alliances,id',
            ]);

            $alliance = Alliance::where('id', $validateData['alliance_id'])->first();

            $totalPoints = $alliance->total_points;
            $cashCut = $totalPoints * 0.01;

            $alliance->total_points = 0;
            $alliance->save();

            $history = new HistoryController();
            $history->logHistory(null, null, $alliance->id, null, null, 4, null, null, $totalPoints, null);

            return $this->apiResponse(true, 'Total de puntos obtenido exitosamente.', ['total_points' => $totalPoints, 'cash_out' => $cashCut], null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener el historial.', null, $e->getMessage() . ' ' . $e->getLine(), 500);
        }
    }
}
