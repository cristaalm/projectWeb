<?php

namespace App\Http\Requests\Containers;

use App\Repositories\ContainerRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListContainersRequest extends FormRequest
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
            'key' => ['nullable', 'string', Rule::in(ContainerRepository::SORTABLE_COLUMNS)],
            'order' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
