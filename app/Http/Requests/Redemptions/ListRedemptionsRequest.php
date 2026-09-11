<?php

namespace App\Http\Requests\Redemptions;

use App\Enums\RewardRedemptionStatus;
use App\Repositories\PointRedemptionRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListRedemptionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'alliance_id' => ['nullable', 'integer', 'exists:alliances,id'],
            'reward_id' => ['nullable', 'integer', 'exists:rewards,id'],
            'status' => ['nullable', Rule::in(array_column(RewardRedemptionStatus::cases(), 'value'))],
            'query' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', Rule::in(PointRedemptionRepository::SORTABLE_COLUMNS)],
            'order' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
