<?php

namespace App\Http\Requests\Badges;

use App\Repositories\BadgeRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListBadgesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::in([0, 1])],
            'query' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', Rule::in(BadgeRepository::SORTABLE_COLUMNS)],
            'order' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
