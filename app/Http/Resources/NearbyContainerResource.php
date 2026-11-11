<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Contenedor de "contenedores cerca de ti" (app móvil), con el último nivel de
 * llenado de cada compartimento. No incluye serial_number ni el token.
 */
class NearbyContainerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $fullLevel = config('nearby.container_full_level');
        $staleBefore = now()->subHours(config('nearby.container_stale_after_hours'));

        return [
            'id' => $this->id,
            'name' => $this->name,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'distance_m' => (int) round((float) $this->distance_m),
            'sensors' => $this->sensors->map(fn ($sensor) => [
                'sensor_key' => $sensor->sensor_key,
                'material' => $sensor->materialType ? [
                    'slug' => $sensor->materialType->slug,
                    'name' => $sensor->materialType->name,
                ] : null,
                'fill_level' => $sensor->fill_level,
                'is_full' => $sensor->fill_level >= $fullLevel,
                // updated_at es la hora de la medición, no la de recepción.
                'measured_at' => $sensor->updated_at?->toJSON(),
                'is_stale' => $sensor->updated_at === null || $sensor->updated_at->lt($staleBefore),
            ])->values()->all(),
        ];
    }
}
