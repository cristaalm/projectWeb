<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Models\Scan;
use App\Models\MaterialType;
use App\Models\User;
use App\Enums\ScanStatus;

class ScanController extends Controller
{
    public function scan(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'container_id' => 'required|exists:containers,id',
                'user_id' => 'required|exists:users,id',
            ]);

            $user = User::findOrFail($validatedData['user_id']);

            if ($user->status == 0) {
                return $this->apiResponse(false, 'Error de validación.', null, 'El usuario no esta activo.', 422);
            }
    
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('temp', $imageName, 'public'); 

            $iaApiUrl = env('URL_IA', null);
            $iaResult = null;
    
            if ($iaApiUrl !== null){
                $imagePath = storage_path('app/public/' . $imagePath);

                $response = Http::withHeaders([
                    'Content-Type' => 'application/octet-stream',
                ])->withBody(
                    file_get_contents($imagePath), 'application/octet-stream'
                )->post($iaApiUrl);
                
                Storage::disk('public')->delete('temp/' . $imageName);
        
                if (!$response->successful()) {
                    return $this->apiResponse(false, 'Error al procesar la imagen', null, 'La IA no respondió correctamente.', 500);
                }
    
                \Log::info('Respuesta IA:', ['body' => $response->body()]);
        
                $iaResult = $response->json();

                \Log::info('Respuesta IA:', ['body' => $iaResult]);
    
                if (is_null($iaResult)) {
                    return $this->apiResponse(false, 'Error al escanear.', null, 'La IA devolvió una respuesta vacía.', 500);
                }
            } else {
                $iaApiUrl = base_path('tests/n8n.json');
                $iaResult = json_decode(file_get_contents($iaApiUrl), true);
            }
    
            if (!isset($iaResult['success']) || $iaResult['success'] !== true) {
                return $this->apiResponse(false, 'Error al escanear.', $iaResult, 'La IA no pudo procesar la imagen correctamente.', 422);
            }
            
            if (!isset($iaResult['tipo'])) {
                return $this->apiResponse(false, 'Error al escanear.', $iaResult, 'La respuesta de la IA no contiene el tipo de material.', 422);
            }    

            $validMaterialType = MaterialType::where('id', $iaResult['tipo'])->first();
            
            if (!$validMaterialType) {
                return $this->apiResponse(false, 'Error al escanear.', $iaResult, 'El tipo de material no es válido.', 422);
            }

            $points =  $validMaterialType->points;
            $points = $iaResult['aplastado'] ? $points + 5 : $points;

            $image = $request->file('image');
            $path = 'scans/container_' . $validatedData['container_id'] . '/user_' . $validatedData['user_id'];
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $path . '/' . $imageName;

            Storage::disk('public')->putFileAs($path, $image, $imageName);

            DB::beginTransaction();

            $user->total_points += $points;
            $user->save();

            $scan = Scan::create([
                'user_id' => $validatedData['user_id'],
                'container_id' => $validatedData['container_id'],
                'material_type_id' => $iaResult['tipo'],
                'image' => $imagePath,
                'scan_status' => ScanStatus::SUCCESS->value,
                'is_valid' =>  $iaResult['reciclable'],
                'points_awarded' => $points,
                'description' => $iaResult['detalle'],
                'scanned_at' => now(),
            ]);

            DB::commit();

            $response = [
                ...$iaResult,
                "tipo" => $validMaterialType->name,
            ];

            return $this->apiResponse(true, 'Escaneo exitoso.', $response);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse(false, 'Error al escanear.', null, $e->getMessage(), 500);
        }
    }
}
