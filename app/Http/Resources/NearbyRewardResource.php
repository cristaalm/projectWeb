<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * Recompensa para "recompensas cerca de ti" (app móvil), con lo que el
 * NearbyService le calculó (can_redeem, points_missing, distance_m) y su
 * comercio en la forma pública de ShopResource. No incluye `code`: es el código
 * que se entrega al canjear.
 */
class NearbyRewardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image_url' => $this->image_url ? Storage::disk('public')->url($this->image_url) : null,
            'points_required' => $this->points_required,
            'stock' => $this->stock,
            'expires_at' => $this->expires_at?->toJSON(),
            'is_exclusive' => $this->is_exclusive,
            'can_redeem' => $this->can_redeem,
            'points_missing' => $this->points_missing,
            'shop' => [
                ...(new ShopResource($this->alliance))->resolve($request),
                'distance_m' => $this->distance_m !== null ? (int) round($this->distance_m) : null,
            ],
        ];
    }
}
