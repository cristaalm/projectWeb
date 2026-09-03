<?php

namespace App\Http\Requests\Alliances;

use Illuminate\Foundation\Http\FormRequest;

class CreateAllianceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'contact_name' => ['required', 'string', 'max:100'],
            'contact_email' => ['required', 'email', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'type_shop_id' => ['required', 'integer', 'exists:type_shop,id'],
            'has_exclusive_rewards' => ['required', 'boolean'],
            'status' => ['required', 'boolean'],
        ];
    }
}
