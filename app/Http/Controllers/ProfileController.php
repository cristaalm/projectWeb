<?php

namespace App\Http\Controllers;

use App\Exceptions\ProfileException;
use App\Http\Controllers\OldControllers\Controller;
use App\Http\Requests\Profile\LinkSocialAccountRequest;
use App\Http\Requests\Profile\UnlinkSocialAccountRequest;
use App\Http\Requests\Profile\UpdateAvatarRequest;
use App\Http\Requests\Profile\UpdateEmailRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(private readonly ProfileService $profileService)
    {
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = $this->profileService->updateProfile($request->user(), $request->validated());

        return $this->apiResponse(true, 'Perfil actualizado correctamente.', [
            'user' => new UserResource($user),
        ], null, 200);
    }

    public function updateAvatar(UpdateAvatarRequest $request)
    {
        $user = $this->profileService->updateAvatar($request->user(), $request->file('avatar'));

        return $this->apiResponse(true, 'Avatar actualizado correctamente.', [
            'user' => new UserResource($user),
        ], null, 200);
    }

    public function deleteAvatar(Request $request)
    {
        $user = $this->profileService->deleteAvatar($request->user());

        return $this->apiResponse(true, 'Avatar eliminado correctamente.', [
            'user' => new UserResource($user),
        ], null, 200);
    }

    public function updateEmail(UpdateEmailRequest $request)
    {
        try {
            $user = $this->profileService->updateEmail(
                $request->user(),
                $request->validated('email'),
                $request->validated('password'),
                $request->validated('token2FA'),
                $request->validated('recovery_code'),
            );

            return $this->apiResponse(true, 'Correo actualizado correctamente.', [
                'user' => new UserResource($user),
            ], null, 200);
        } catch (ProfileException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        try {
            $user = $this->profileService->updatePassword(
                $request->user(),
                $request->validated('current_password'),
                $request->validated('password'),
                $request->validated('token2FA'),
                $request->validated('recovery_code'),
            );

            return $this->apiResponse(true, 'Contraseña actualizada correctamente.', [
                'user' => new UserResource($user),
            ], null, 200);
        } catch (ProfileException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function linkSocialAccount(LinkSocialAccountRequest $request)
    {
        try {
            $user = $this->profileService->linkSocialAccount(
                $request->user(),
                $request->validated('provider'),
                $request->validated('id_token'),
            );

            return $this->apiResponse(true, 'Cuenta vinculada correctamente.', [
                'user' => new UserResource($user),
            ], null, 200);
        } catch (ProfileException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }

    public function unlinkSocialAccount(UnlinkSocialAccountRequest $request)
    {
        try {
            $user = $this->profileService->unlinkSocialAccount(
                $request->user(),
                $request->validated('provider'),
                $request->validated('password'),
                $request->validated('token2FA'),
                $request->validated('recovery_code'),
            );

            return $this->apiResponse(true, 'Cuenta desvinculada correctamente.', [
                'user' => new UserResource($user),
            ], null, 200);
        } catch (ProfileException $e) {
            return $this->apiResponse(false, $e->getMessage(), null, $e->details, $e->status);
        }
    }
}
