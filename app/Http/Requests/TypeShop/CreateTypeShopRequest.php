<?php

namespace App\Http\Requests\TypeShop;

use Illuminate\Foundation\Http\FormRequest;

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
            'is_active' => ['required', 'boolean'],
        ];
    }
}
