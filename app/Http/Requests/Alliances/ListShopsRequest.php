<?php

namespace App\Http\Requests\Alliances;

use Illuminate\Foundation\Http\FormRequest;

class ListShopsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_shop_id' => ['nullable', 'integer', 'exists:type_shop,id'],
            'query' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
