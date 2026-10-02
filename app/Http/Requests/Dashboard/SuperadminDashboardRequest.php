<?php

namespace App\Http\Requests\Dashboard;

use App\Services\DashboardService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SuperadminDashboardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period' => ['nullable', 'string', Rule::in(DashboardService::PERIODS)],
        ];
    }
}
