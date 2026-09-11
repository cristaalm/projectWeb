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
            'avatar' => $this->avatar,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'tour' => $this->tour,
            'has_usable_password' => $this->has_usable_password,
            'social_providers' => $this->whenLoaded('socialAccounts', fn () => $this->socialAccounts->pluck('provider')),
            'two_factor_status' => $this->two_factor_status,
            'code_identity' => $this->code_identity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'points_balance' => $this->when(! is_null($this->points_balance), fn () => (int) $this->points_balance),
            'alliance' => $this->when(
                $this->relationLoaded('merchant') || $this->relationLoaded('organizationMember'),
                function () {
                    $alliance = $this->currentAlliance();

                    return $alliance ? [
                        'id' => $alliance->id,
                        'name' => $alliance->name,
                        'phone' => $alliance->phone,
                        'address' => $alliance->address,
                        'logo_url' => $alliance->logo_url,
                        'has_exclusive_rewards' => $alliance->has_exclusive_rewards,
                        'type_shop' => $alliance->relationLoaded('typeShop') && $alliance->typeShop ? [
                            'id' => $alliance->typeShop->id,
                            'name' => $alliance->typeShop->name,
                        ] : null,
                        'status' => $alliance->status,
                        'created_at' => $alliance->created_at,
                        'updated_at' => $alliance->updated_at,
                    ] : null;
                }
            ),
            'role' => $this->whenLoaded('role', fn () => [
                'id' => $this->role->id,
                'name' => $this->role->name,
                'display_name' => $this->role->display_name,
                'is_active' => $this->role->is_active,
            ]),
        ];
    }
}
