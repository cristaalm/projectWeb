<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use App\Notifications\DocumentsVerificationNotification;

use App\Models\User;
use App\Models\IdentityVerification;

use App\Enums\IndentifyVerificationStatus;
use App\Enums\VerificationStatus;

class IdentifyVerificationController extends Controller
{
    public function toggleStatusPending(Request $request, int $userId)
    {
        try {
            $authUser = $request->user();

            $request->merge([
                'user_id' => $userId,
            ]);
            
            $validateData = $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);
            
            DB::beginTransaction();
            
            $user = User::findOrFail($userId);
            $identityVerification = IdentityVerification::where('user_id', $user->id)->first();

            $user->verification_status = VerificationStatus::PENDING->value;
            $user->save();

            $identityVerification->status = IndentifyVerificationStatus::PENDING->value;
            $identityVerification->rejection_reason = null;
            $identityVerification->save();

            DB::commit();
            
            return $this->apiResponse(true, 'Estado actualizado exitosamente.', null, null, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse(false, 'Error al actualizar el estado.', null, $e->getMessage(), 500);
        }
    }

    public function uploadDoc(Request $request, string $type, int $userId)
    {
        try {
            $authUser = $request->user();

            $request->merge([
                'type' => $type,
                'user_id' => $userId,
            ]);
            
            $validateData = $request->validate([
                'document' => 'required|image|mimes:jpeg,png,jpg',
                'type' => 'required|in:ine_front,ine_back,selfie',
                'user_id' => 'required|exists:users,id',
            ]);
            
            $user = User::findOrFail($userId);
            $identityVerification = IdentityVerification::where('user_id', $user->id)->first();

            $document = $request->file('document');
            $documentName = $type . '.' . $document->getClientOriginalExtension();

            DB::beginTransaction();

            $documentPath = $document->storeAs('users/user_' . $user->id, $documentName, 'local');

            $type_url = $type . '_url';

            $identityVerification->$type_url = $documentPath;
            $identityVerification->save();

            DB::commit();
            
            return $this->apiResponse(true, 'Documento subido exitosamente.', $identityVerification, null, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse(false, 'Error al subir los documentos.', null, $e->getMessage(), 500);
        }
    }

    public function uploadDocuments(Request $request) 
    {
        try {
            $authUser = $request->user();
    
            $validateData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'document_front' => 'required|image|mimes:jpeg,png,jpg',
                'document_back' => 'required|image|mimes:jpeg,png,jpg',
            ]);
    
            $user = User::findOrFail($validateData['user_id']);
            $identityVerification = IdentityVerification::where('user_id', $user->id)->first();
    
            $documentFront = $request->file('document_front');
            $documentBack = $request->file('document_back');
    
            $documentFrontName = 'ine_front' . $documentFront->getClientOriginalExtension();
            $documentBackName = 'ine_back' . $documentBack->getClientOriginalExtension();
    
            DB::beginTransaction();
    
            $documentFrontPath = $documentFront->storeAs('users/user_' . $user->id, $documentFrontName, 'local');
            $documentBackPath = $documentBack->storeAs('users/user_' . $user->id, $documentBackName, 'local');
    
            $identityVerification->ine_front_url = $documentFrontPath;
            $identityVerification->ine_back_url = $documentBackPath;
            $identityVerification->status = IndentifyVerificationStatus::PENDING->value;
            $identityVerification->save();
    
            $user->verification_status = VerificationStatus::PENDING->value;
            $user->save();
    
            DB::commit();
    
            return $this->apiResponse(true, 'Documentos subidos exitosamente.', $identityVerification, null, 200);
        } catch (ValidationException $e) {
            // Extraer el primer mensaje de error para devolverlo de forma clara
            $errors = $e->validator->errors()->all();
            $firstError = $errors[0] ?? 'Error de validación en los documentos.';
            
            // Mensaje específico si el error es por tamaño
            if (str_contains($firstError, 'may not be greater than')) {
                $firstError = 'El tamaño de la imagen no debe exceder los 5 MB.';
            }
    
            return $this->apiResponse(false, $firstError, null, null, 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse(false, 'Error al subir los documentos.', null, $e->getMessage(), 500);
        }
    }

    public function uploadSelfie(Request $request) 
    {
        try {
            $authUser = $request->user();

            $validateData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'selfie' => 'required|image|mimes:jpeg,png,jpg',
            ]);

            $user = User::findOrFail($validateData['user_id']);
            $identityVerification = IdentityVerification::where('user_id', $user->id)->first();

            $selfie = $request->file('selfie');
            $selfieName = 'selfie' . $selfie->getClientOriginalExtension();

            DB::beginTransaction();

            $selfiePath = $selfie->storeAs('users/user_' . $user->id, $selfieName, 'local');

            $identityVerification->selfie_url = $selfiePath;
            $identityVerification->status = IndentifyVerificationStatus::PENDING->value;
            $identityVerification->save();

            $user->verification_status = VerificationStatus::PENDING->value;
            $user->save();

            DB::commit();

            return $this->apiResponse(true, 'Selfie subido exitosamente.', $identityVerification, null, 200);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors()->all();
            $firstError = $errors[0] ?? 'Error de validación en el selfie.';

            // Mensaje específico si el error es por tamaño del archivo
            if (str_contains($firstError, 'may not be greater than')) {
                $firstError = 'El tamaño de la imagen no debe exceder los 5 MB.';
            }

            return $this->apiResponse(false, $firstError, null, null, 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->apiResponse(false, 'Error al subir el selfie.', null, $e->getMessage(), 500);
        }
    }

    public function getDocument(Request $request, string $type, int $userId)
    {
        try {
            $authUser = $request->user();
            $token = $request->bearerToken();
            
            // verificación del admin o moderador
            $accessToken = PersonalAccessToken::findToken($token);

            // obtener documentos

            $identityVerification = IdentityVerification::where('user_id', $userId)->first();
            if (!$identityVerification) {
                return $this->apiResponse(false, 'No se encontraron documentos para este usuario.', null, null, 404);
            }

            if (!in_array($type, ['front', 'back', 'selfie'])) {
                return $this->apiResponse(false, 'Tipo de documento no válido.', null, null, 400);
            }
            
            switch ($type) {
                case 'front':
                    $filePath = $identityVerification->ine_front_url;
                    break;
                case 'back':
                    $filePath = $identityVerification->ine_back_url;
                    break;
                case 'selfie':
                    $filePath = $identityVerification->selfie_url;
                    break;
                default:
                    $filePath = null;
            }

            if (!$filePath || !Storage::disk('local')->exists($filePath)) {
                return $this->apiResponse(false, 'Documento no encontrado.', null, null, 404);
            }
    
            $file = Storage::disk('local')->readStream($filePath);
            $mimeType = Storage::disk('local')->mimeType($filePath);
            $fileName = basename($filePath);
    
            return new StreamedResponse(function () use ($file) {
                fpassthru($file);
            }, 200, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => "inline; filename=\"{$fileName}\"",
            ]);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener los documentos.', null, $e->getMessage(), 500);
        }
    }

    public function getListDocs(Request $request)
    {
        try {
            $authUser = $request->user();
            $validateData = $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);
            
            $docs = [
                'front' => false,
                'back' => false,
                'selfie' => false,
            ];

            $identityVerification = IdentityVerification::where('user_id', $validateData['user_id'])->first();

            if (!$identityVerification) {
                return $this->apiResponse(false, 'No se encontraron documentos para este usuario.', $docs, null, 200);
            }

            if ($identityVerification->ine_front_url) {
                // verificamos que exista el archivo
                if (!Storage::disk('local')->exists($identityVerification->ine_front_url)) {
                    $identityVerification->ine_front_url = null;
                } else {
                    $docs['front'] = true;
                }
            }

            if ($identityVerification->ine_back_url) {
                // verificamos que exista el archivo
                if (!Storage::disk('local')->exists($identityVerification->ine_back_url)) {
                    $identityVerification->ine_back_url = null;
                } else {
                    $docs['back'] = true;
                }
            }

            if ($identityVerification->selfie_url) {
                // verificamos que exista el archivo
                if (!Storage::disk('local')->exists($identityVerification->selfie_url)) {
                    $identityVerification->selfie_url = null;
                } else {
                    $docs['selfie'] = true;
                }
            }

            $identityVerification->save();

            return $this->apiResponse(true, 'Lista de documentos obtenida exitosamente.', $docs, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al obtener la lista de documentos.', null, $e->getMessage(), 500);
        }
    }

    public function verificationUser(Request $request)
    {
        try {
            $authUser = $request->user();
            $validateData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'justification' => 'nullable|string',
                'status' => 'required|integer|in:1,2',
            ]);
            
            $user = User::findOrFail($validateData['user_id']);
            $identityVerification = IdentityVerification::where('user_id', $user->id)->first();

            if (!$identityVerification) {
                return $this->apiResponse(false, 'No se encontraron documentos para este usuario.', null, null, 422);
            }


            $identityVerification->status = $validateData['status'];
            if ($validateData['status'] == 2 && !$validateData['justification']) {
                return $this->apiResponse(false, 'Justificación es requerida para rechazar la verificación.', null, null, 422);
            } else if ($validateData['status'] == 2 && $validateData['justification']) {
                $identityVerification->rejection_reason = $validateData['justification'];
            }
            $identityVerification->verified_by = $authUser->id;
            $identityVerification->verified_at = now();
            $identityVerification->save();

            $user->verification_status = $validateData['status'];
            $user->save();

            // 👇 ENVIAR NOTIFICACIÓN AL USUARIO
            $user->notify(new DocumentsVerificationNotification(
                status: $validateData['status'],
                justification: $validateData['justification'] ?? null
            ));


            return $this->apiResponse(true, 'Usuario verificado exitosamente.', null, null, 200);
        } catch (\Exception $e) {
            return $this->apiResponse(false, 'Error al verificar el usuario.', null, $e->getMessage(), 500);
        }
    }
}
