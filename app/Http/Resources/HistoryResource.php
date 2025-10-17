<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
        'user_id' => $this->user_id,
        'type_history' => $this->type_history,
        'scan_id' => $this->scan_id,
        'scan' => $this->scan ? $this->whenLoaded('scan', [
            'id' => $this->scan->id,
            'name' => $this->scan->name,
            'display_name' => $this->scan->display_name,
            'is_active' => $this->scan->is_active,
        ]) : null,
        'material_type_id' => $this->material_type_id,
        'material_type' => $this->material_type ? $this->whenLoaded('material_type', [
            'id' => $this->material_type->id,
            'name' => $this->material_type->name,
            'slug' => $this->material_type->slug,
            'points' => $this->material_type->points,
            'is_active' => $this->material_type->is_active,
            'description' => $this->material_type->description,
        ]) : null,
        'reward_id' => $this->reward_id,
        'reward' => $this->reward ? $this->whenLoaded('reward', [
            'id' => $this->reward->id,
            'name' => $this->reward->name,
            'display_name' => $this->reward->display_name,
            'is_active' => $this->reward->is_active,
        ]) : null,
        'points' => $this->points,
        'quantity' => $this->quantity,
        'alliance_id' => $this->alliance_id,
        'alliance' => $this->alliance ? $this->whenLoaded('alliance', [
            'id' => $this->alliance->id,
            'name' => $this->alliance->name,
            'display_name' => $this->alliance->display_name,
            'is_active' => $this->alliance->is_active,
        ]) : null,
        ];
    }
}
