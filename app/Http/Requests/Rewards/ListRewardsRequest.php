<?php

namespace App\Http\Requests\Rewards;

use App\Enums\RewardStatus;
use App\Repositories\RewardRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListRewardsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'alliance_id' => ['nullable', 'integer', 'exists:alliances,id'],
            'status' => ['nullable', Rule::in(array_column(RewardStatus::cases(), 'value'))],
            'query' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', Rule::in(RewardRepository::SORTABLE_COLUMNS)],
            'order' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
