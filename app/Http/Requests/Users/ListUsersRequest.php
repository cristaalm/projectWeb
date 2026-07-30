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
            'search' => ['nullable', 'string', 'max:255'],
            'sort_by' => ['nullable', 'string', Rule::in(UserRepository::SORTABLE_COLUMNS)],
            'sort_dir' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'with_trashed' => ['nullable', 'boolean'],
        ];
    }
}
