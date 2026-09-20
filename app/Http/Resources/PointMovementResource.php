<?php

namespace App\Http\Resources;

use App\Enums\PointMovementType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * Da forma a una fila de `PointRepository::movements()` (stdClass, no un modelo Eloquent).
 */
class PointMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $type = PointMovementType::from($this->type);

        return [
            'type' => $type->value,
            'label' => $type->label(),
            'description' => $this->description,
            'points' => (int) $this->points,
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->toJSON() : null,
        ];
    }
}
