<?php

namespace App\Http\Requests\Rewards;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRewardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Reasignar de alianza solo lo puede hacer staff — RewardService
            // ignora este campo si quien edita es admin_merchant.
            'alliance_id' => ['nullable', 'integer', 'exists:alliances,id'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'points_required' => ['required', 'integer', 'min:1'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'is_exclusive' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date'],
        ];
    }
}
