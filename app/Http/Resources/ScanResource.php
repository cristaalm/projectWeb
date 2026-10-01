<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * Forma única de un escaneo, tanto para la respuesta al contenedor
 * (POST /scans) como para el listado administrativo (GET /scans). `user` y
 * `container` solo aparecen cuando la relación viene cargada (listado).
 */
class ScanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->event_id,
            'status' => $this->scan_status->value,
            'status_label' => $this->scan_status->label(),
            'material' => $this->materialType ? [
                'id' => $this->materialType->id,
                'slug' => $this->materialType->slug,
                'name' => $this->materialType->name,
            ] : null,
            'points_awarded' => $this->points_awarded,
            'description' => $this->description,
            'image_url' => $this->image ? Storage::disk('public')->url($this->image) : null,
            'scanned_at' => $this->scanned_at?->toJSON(),
            'user' => $this->whenLoaded('user', fn () => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'last_name' => $this->user->last_name,
                'email' => $this->user->email,
                'code_identity' => $this->user->code_identity,
                'avatar' => $this->user->avatar,
            ] : null),
            'container' => $this->whenLoaded('container', fn () => $this->container ? [
                'id' => $this->container->id,
                'name' => $this->container->name,
                'serial_number' => $this->container->serial_number,
            ] : null),
        ];
    }
}
