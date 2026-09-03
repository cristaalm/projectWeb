<?php

namespace App\Http\Requests\TypeShop;

use App\Repositories\TypeShopRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListTypeShopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_active' => ['nullable', Rule::in([0, 1])],
            'query' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', Rule::in(TypeShopRepository::SORTABLE_COLUMNS)],
            'order' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
