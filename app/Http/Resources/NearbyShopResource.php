<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

/**
 * Comercio de "comercios cerca de ti" (app móvil): la forma pública de
 * ShopResource más su distancia y cuántas recompensas tiene para el usuario.
 */
class NearbyShopResource extends ShopResource
{
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'distance_m' => (int) round((float) $this->distance_m),
            'available_rewards_count' => (int) $this->available_rewards_count,
            'redeemable_rewards_count' => (int) $this->redeemable_rewards_count,
        ];
    }
}
