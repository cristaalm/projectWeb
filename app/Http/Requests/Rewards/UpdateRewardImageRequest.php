<?php

namespace App\Http\Requests\Rewards;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRewardImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }
}
