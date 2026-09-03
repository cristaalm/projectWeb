<?php

namespace App\Http\Requests\Alliances;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAllianceLogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'logo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }
}
