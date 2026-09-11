<?php

namespace App\Http\Requests\Rewards;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRewardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // admin_merchant no elige alianza — RewardService la fuerza a la
            // suya propia (currentAlliance()) e ignora este campo si lo manda.
            'alliance_id' => [
                Rule::requiredIf(fn () => $this->isStaff()),
                'nullable', 'integer', 'exists:alliances,id',
            ],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'points_required' => ['required', 'integer', 'min:1'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'is_exclusive' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'alliance_id.required' => 'Selecciona la alianza para la que se crea la recompensa.',
        ];
    }

    private function isStaff(): bool
    {
        return in_array($this->user()?->role?->name, ['superadmin', 'moderador'], true);
    }
}
