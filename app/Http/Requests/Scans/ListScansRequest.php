<?php

namespace App\Http\Requests\Scans;

use App\Enums\ScanStatus;
use App\Repositories\ScanRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListScansRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::in(array_column(ScanStatus::cases(), 'value'))],
            'container_id' => ['nullable', 'integer', 'exists:containers,id'],
            'material_type_id' => ['nullable', 'integer', 'exists:material_types,id'],
            'query' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', Rule::in(ScanRepository::SORTABLE_COLUMNS)],
            'order' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
