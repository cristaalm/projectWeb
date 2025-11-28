<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserWithIdentityResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'alliance_id' => $this->alliance_id,
            'avatar' => $this->avatar,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'curp' => $this->curp,
            'total_points' => $this->total_points,
            'tour' => $this->tour,
            'verification_status' => $this->verification_status,
            'two_factor_status' => $this->two_factor_status,
            'code_identity' => $this->code_identity,
            'status' => $this->status,
            'badge' => $this->badge ?? [],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'alliance' => $this->alliance_id ? $this->whenLoaded('alliance', [
                'id' => $this->alliance->id,
                'name' => $this->alliance->name,
                'phone' => $this->alliance->phone,
                'logo' => $this->alliance->logo,
                'address' => $this->alliance->address,
                'type_shop' => $this->alliance->type_shop_id ? $this->whenLoaded('typeShop', [
                    'id' => $this->alliance->typeShop->id,
                    'name' => $this->alliance->typeShop->name,
                ]) : null,
                'total_points' => $this->alliance->total_points,
                'ext' => $this->alliance->ext,
                'created_at' => $this->alliance->created_at,
                'updated_at' => $this->alliance->updated_at,
            ]) : null,
            'identity_verification' => $this->whenLoaded('identityVerification', [
                'id' => $this->identityVerification->id,
                'status' => $this->identityVerification->status,
                'ine_front_url' => $this->identityVerification->ine_front_url,
                'ine_back_url' => $this->identityVerification->ine_back_url,
                'selfie_url' => $this->identityVerification->selfie_url,
                'document_number' => $this->identityVerification->document_number,
                'rejection_reason' => $this->identityVerification->rejection_reason,
                'verified_by' => $this->identityVerification->verified_by,
                'verified_at' => $this->identityVerification->verified_at,
                'created_at' => $this->identityVerification->created_at,
                'updated_at' => $this->identityVerification->updated_at,
            ]),
            'role' => $this->whenLoaded('role', [
                'id' => $this->role->id,
                'name' => $this->role->name,
                'display_name' => $this->role->display_name,
                'is_active' => $this->role->is_active,
            ]),
        ];
    }
}
