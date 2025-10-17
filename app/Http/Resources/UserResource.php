<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'alliance_id' => $this->alliance_id,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'curp' => $this->curp,
            'total_points' => $this->total_points,
            'verification_status' => $this->verification_status,
            'two_factor_status' => $this->two_factor_status,
            'code_identity' => $this->code_identity,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'alliance' => $this->alliance_id ? $this->whenLoaded('alliance', [
                'id' => $this->alliance->id,
                'name' => $this->alliance->name,
                'phone' => $this->alliance->phone,
                'logo' => $this->alliance->logo,
                'ext' => $this->alliance->ext,
                'created_at' => $this->alliance->created_at,
                'updated_at' => $this->alliance->updated_at,
            ]) : null,
            'role' => $this->role_id ? $this->whenLoaded('role', [
                'id' => $this->role->id,
                'name' => $this->role->name,
                'display_name' => $this->role->display_name,
                'is_active' => $this->role->is_active,
            ]) : null,
        ];
    }
}
