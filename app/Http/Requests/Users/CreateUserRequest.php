<?php

namespace App\Http\Requests\Users;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role_id' => [
                'required', 'integer', 'exists:roles,id',
                Rule::notIn([Role::firstWhere('name', 'superadmin')?->id]),
            ],
            'alliance_id' => [
                Rule::requiredIf(fn () => in_array($this->resolveRoleName(), ['admin_merchant', 'merchant'], true)),
                'nullable', 'integer', 'exists:alliances,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'role_id.not_in' => 'No se puede crear una cuenta de superadministrador desde este módulo.',
            'alliance_id.required' => 'Este rol requiere estar asignado a un comercio (alianza).',
        ];
    }

    private function resolveRoleName(): ?string
    {
        $roleId = $this->input('role_id');

        if (! $roleId) {
            return null;
        }

        return Role::find($roleId)?->name;
    }
}
