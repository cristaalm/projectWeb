<?php

namespace App\Http\Requests\Badges;

use Illuminate\Foundation\Http\FormRequest;

class ClaimBadgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'badge_id' => ['required', 'integer', 'exists:badge,id'],
            'month' => ['required', 'date_format:Y-m-d'],
        ];
    }
}
