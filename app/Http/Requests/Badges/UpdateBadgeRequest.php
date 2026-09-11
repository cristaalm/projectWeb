<?php

namespace App\Http\Requests\Badges;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBadgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', Rule::in(CreateBadgeRequest::CURATED_ICONS)],
            'recycles_required' => ['required', 'integer', 'min:1'],
            'points_awarded' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'boolean'],
        ];
    }
}
