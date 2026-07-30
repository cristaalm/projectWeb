<?php

namespace App\Http\Resources;

use App\Enums\UserAccountActionType;
use App\Models\PointAdjustment;
use App\Models\UserAccountAction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAccountActionResource extends JsonResource
{
    public function toArray(Request $request)
    {
        if ($this->resource instanceof PointAdjustment) {
            return [
                'id' => 'points_adjustment_' . $this->id,
                'type' => 'points_adjustment',
                'action_type' => 'points_adjustment',
                'label' => 'Ajuste de puntos',
                'reason' => $this->reason,
                'points' => (int) $this->points,
                'actor' => $this->whenLoaded('admin', fn () => [
                    'id' => $this->admin->id,
                    'name' => $this->admin->name,
                    'last_name' => $this->admin->last_name,
                ]),
                'created_at' => $this->created_at,
            ];
        }

        /** @var UserAccountAction $this */
        return [
            'id' => 'account_action_' . $this->id,
            'type' => 'account_action',
            'action_type' => $this->action_type->value,
            'label' => $this->action_type instanceof UserAccountActionType
                ? $this->action_type->label()
                : $this->action_type,
            'reason' => $this->reason,
            'points' => null,
            'actor' => $this->whenLoaded('actorUser', fn () => [
                'id' => $this->actorUser->id,
                'name' => $this->actorUser->name,
                'last_name' => $this->actorUser->last_name,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
