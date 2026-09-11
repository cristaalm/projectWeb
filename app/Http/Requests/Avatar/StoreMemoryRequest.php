<?php

namespace App\Http\Requests\Avatar;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
