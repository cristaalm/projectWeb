<?php

namespace App\Http\Requests\Badges;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateBadgeRequest extends FormRequest
{
    /**
     * Set curado de íconos boxicon (bx-*) temáticos de reciclaje/logro
     * ofrecidos en el selector del formulario de insignias.
     */
    public const CURATED_ICONS = [
        'bx-recycle',
        'bx-leaf',
        'bx-bxs-tree',
        'bx-water',
        'bx-world',
        'bx-medal',
        'bx-trophy',
        'bx-award',
        'bx-badge-check',
        'bx-sun',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'icon' => ['nullable', 'string', Rule::in(self::CURATED_ICONS)],
            'recycles_required' => ['required', 'integer', 'min:1'],
            'points_awarded' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'boolean'],
        ];
    }
}
