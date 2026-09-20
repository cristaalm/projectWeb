<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * Forma pública de un comercio (App\Models\Alliance) para la app móvil: sin datos
 * de contacto administrativo y con la URL del logo ya absoluta (la columna guarda
 * solo la ruta relativa dentro del disco `public`).
 */
class ShopResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'logo_url' => $this->logo_url ? Storage::disk('public')->url($this->logo_url) : null,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'has_exclusive_rewards' => $this->has_exclusive_rewards,
            'type_shop' => $this->typeShop ? [
                'id' => $this->typeShop->id,
                'name' => $this->typeShop->name,
            ] : null,
        ];
    }
}
