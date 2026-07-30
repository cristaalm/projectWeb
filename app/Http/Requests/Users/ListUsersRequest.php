<?php

namespace App\Http\Requests\Users;

use App\Repositories\UserRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListUsersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => ['nullable', 'string', 'exists:roles,name'],
            'alliance_id' => ['nullable', 'integer', 'exists:alliances,id'],
            'points_min' => ['nullable', 'integer'],
            'points_max' => ['nullable', 'integer'],
            'query' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', Rule::in(UserRepository::SORTABLE_COLUMNS)],
            'order' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            // Sin regla 'boolean' estricta a propósito: axios serializa un booleano JS
            // como el string "false" en query string, y Laravel::boolean solo acepta
            // true/false/0/1/'0'/'1' — filter_var() en el repositorio normaliza esto.
            'with_trashed' => ['nullable'],
        ];
    }
}
