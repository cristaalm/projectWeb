<?php

namespace App\Http\Requests\Scans;

use Illuminate\Foundation\Http\FormRequest;

class IdentifyScanUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code_identity' => ['required', 'string', 'max:30'],
        ];
    }
}
