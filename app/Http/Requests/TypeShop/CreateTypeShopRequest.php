<?php

namespace App\Http\Requests\TypeShop;

use App\Support\TypeShopIcons;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTypeShopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'icon' => ['required', 'string', Rule::in(TypeShopIcons::names())],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'icon.in' => 'El icono seleccionado no está en el catálogo de iconos.',
        ];
    }
}
