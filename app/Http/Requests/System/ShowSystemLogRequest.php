<?php

namespace App\Http\Requests\System;

use App\Services\SystemLogService;
use Illuminate\Foundation\Http\FormRequest;

class ShowSystemLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lines' => ['nullable', 'integer', 'min:1', 'max:'.SystemLogService::MAX_LINES],
            'search' => ['nullable', 'string', 'max:100'],
        ];
    }
}
