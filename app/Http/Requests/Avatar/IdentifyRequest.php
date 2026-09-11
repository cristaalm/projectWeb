<?php

namespace App\Http\Requests\Avatar;

use Illuminate\Foundation\Http\FormRequest;

class IdentifyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code_identity' => ['required', 'string', 'max:30'],
            'container_serial_number' => ['required', 'string', 'max:255'],
        ];
    }
}
