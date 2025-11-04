<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Alliance;
use App\Models\User;
use App\Models\Reward;
use App\Models\History;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

            $validated = $request->validate([
                'alliance_id' => 'required|exists:alliances,id',
                'only_return' => 'nullable|string',
            ]);
            
            $onlyReturn = filter_var($request->only_return, FILTER_VALIDATE_BOOLEAN);
        
            $alliance = Alliance::findOrFail($validated['alliance_id']);
            $totalPoints = $alliance->total_points;
            $cashCut = $totalPoints * 0.01;
        
            if (!$onlyReturn) {
                Log::info('Se hizo el corte de caja, para la alianza: ' . $alliance->id);
        
                DB::beginTransaction();
                try {
                    $alliance->total_points = 0;
                    $alliance->save();
        
                    $history = new HistoryController();
                    $history->logHistory(null, null, $alliance->id, null, null, 4, null, null, $totalPoints, null);
        
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    return $this->apiResponse(false, 'Error al procesar el corte de caja.', null, $e->getMessage(), 500);
                }
            } else {
                Log::info('Solo se retornaron los puntos de la alianza: ' . $alliance->id);
            }
        
            return $this->apiResponse(
                true,
                'Total de puntos obtenido exitosamente.',
                ['total_points' => $totalPoints, 'cash_out' => $cashCut],
                null,
                200
            );
        } catch (ValidationException $e) {
            return $this->apiResponse(false, 'Error de validación.', null, $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al procesar el corte de caja.', null, $e->getMessage(), 500);
        }
    }

    public function getStatsByShop(Request $request, int $alliance_id) 
    {
        try {

            $alliance = Alliance::findOrFail($alliance_id);

            $stats = [
                "total_income" => 0,
                "total_points_awarded" => 0,
                "average_total_income" => 0,
                "total_customers_served" => 0,
            ];

            // TOTAL_INCOME
            try {
                $total = History::where('alliance_id', $alliance_id)->where('type_history', 4)->sum('points');
                $total_income = $total * 0.01;
                $stats['total_income'] = abs($total_income);
            } catch (\Exception $e) {
                return $this->apiResponse(false, 'Error al obtener el total de ingresos.', null, $e->getMessage(), 500);
            }

            // TOTAL_POINTS_AWARDED

            try {
                $total_points_awarded = History::where('alliance_id', $alliance_id)->where('type_history', 1)->sum('points');
                $stats['total_points_awarded'] = abs($total_points_awarded);
            } catch (\Exception $e) {
                return $this->apiResponse(false, 'Error al obtener el total de puntos otorgados.', null, $e->getMessage(), 500);
            }

            // AVERAGE_TOTAL_INCOME

            try {
                $average_total_income = History::where('alliance_id', $alliance_id)->where('type_history', 4)->avg('points');
                $average_total_income = $average_total_income * 0.01;
                $stats['average_total_income'] = abs($average_total_income);
            } catch (\Exception $e) {
                return $this->apiResponse(false, 'Error al obtener el promedio de ingresos.', null, $e->getMessage(), 500);
            }

            // TOTAL_CUSTOMERS_SERVED
            try {
                $total_customers_served = History::where('alliance_id', $alliance_id)->where('type_history', 1)->count();
                $stats['total_customers_served'] = $total_customers_served;
            } catch (\Exception $e) {
                return $this->apiResponse(false, 'Error al obtener el total de clientes atendidos.', null, $e->getMessage(), 500);
            }

            return $this->apiResponse(true, 'Estadisticas obtenidas exitosamente.', $stats, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener las estadisticas.', null, $e->getMessage(), 500);
        }
    }

    public function getActivityByDayOfWeek(Request $request, int $alliance_id)
    {
        try {
            // Validar existencia de alianza
            $alliance = Alliance::findOrFail($alliance_id);

            $timezone = 'America/Mexico_City';

            // Definir nombres de días en español
            $daysInSpanish = [
                'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'
            ];

            // === SEMANA ACTUAL: Lunes a Domingo ===
            $mondayThisWeek = Carbon::now($timezone)->startOfWeek(Carbon::MONDAY);
            $sundayThisWeek = $mondayThisWeek->copy()->addDays(6);

            $statsToWeek = [];

            foreach (range(0, 6) as $offset) {
                $currentDay = $mondayThisWeek->copy()->addDays($offset);
                $dayName = $daysInSpanish[$offset];
                $dateFormatted = $currentDay->format('d/m/Y');

                $count = $alliance->history()
                    ->where('type_history', 1)
                    ->whereDate('created_at', $currentDay)
                    ->count();

                $statsToWeek[] = [
                    'day' => $dayName,
                    'date' => $dateFormatted,
                    'total_activity' => $count
                ];
            }

            // === SEMANA PASADA: Lunes anterior a Domingo anterior ===
            $mondayLastWeek = $mondayThisWeek->copy()->subWeek();
            $sundayLastWeek = $sundayThisWeek->copy()->subWeek();

            // Contar canjes (type_history = 1) en semana pasada
            $salesLastWeek = $alliance->history()
                ->where('type_history', 1)
                ->whereBetween('created_at', [$mondayLastWeek, $sundayLastWeek])
                ->get();

            $totalSales = $salesLastWeek->count(); // Número de canjes
            $totalPoints = $salesLastWeek->sum('points'); // Suma de puntos en esos canjes

            // Respuesta final
            $response = [
                'statsToWeek' => $statsToWeek,
                'totalSales' => abs((int) $totalSales),
                'totalPoints' => abs((int) $totalPoints),
            ];

            return $this->apiResponse(true, 'Actividad del comercio por día de la semana y métricas de la semana pasada.', $response);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->apiResponse(false, 'El comercio que intenta consultar no existe.', null, null, 404);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener la actividad del comercio.', null, $e->getMessage(), 500);
        }
    }

    public function getTopRewardsByAlliance(Request $request, int $alliance_id)
    {
        try {
            // Validar que la alianza exista
            $alliance = Alliance::find($alliance_id);
            if (! $alliance) {
                return $this->apiResponse(false, 'La alianza especificada no existe.', null, null, 404);
            }

            // Obtener los IDs de recompensas más canjeadas (type_history = 1)
            $topRewardStats = History::where('alliance_id', $alliance_id)
                ->where('type_history', 1) // Canjes de recompensas
                ->whereNotNull('reward_id')
                ->selectRaw('reward_id, count(*) as total_claimed')
                ->groupBy('reward_id')
                ->orderByDesc('total_claimed')
                ->limit(3)
                ->get();

            if ($topRewardStats->isEmpty()) {
                return $this->apiResponse(true, 'No hay canjes registrados para esta alianza.', [], null, 200);
            }

            // Obtener los IDs de recompensa
            $rewardIds = $topRewardStats->pluck('reward_id')->toArray();

            // Obtener los detalles completos de las recompensas (incluyendo soft deleted)
            $rewards = Reward::withTrashed()
                ->whereIn('id', $rewardIds)
                ->get()
                ->keyBy('id'); // Para acceso rápido por ID

            // Construir resultado final
            $result = $topRewardStats->map(function ($item) use ($rewards) {
                $reward = $rewards[$item->reward_id] ?? null;

                return [
                    'reward_id' => $item->reward_id,
                    'reward_name' => $reward ? $reward->name : 'Recompensa eliminada',
                    'total_claimed' => (int) $item->total_claimed,
                ];
            })->sortBy([['total_claimed', 'desc'], ['reward_name', 'asc']]) // Por si hay mismo conteo, ordena alfabéticamente
            ->values()
            ->toArray();

            return $this->apiResponse(true, 'Recompensas más canjeadas obtenidas exitosamente.', $result);

        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener las recompensas más canjeadas.', null, $e->getMessage(), 500);
        }
    }
}
