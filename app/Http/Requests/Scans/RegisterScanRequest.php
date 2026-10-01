<?php

namespace App\Http\Requests\Scans;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterScanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'uuid'],
            'code_identity' => ['required', 'string', 'max:30'],
            'container_serial_number' => ['required', 'string', 'max:255'],
            'material' => ['required', 'string', Rule::exists('material_types', 'slug')],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'material.exists' => 'El material indicado no existe en el catálogo.',
        ];
    }
}
