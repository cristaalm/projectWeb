<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;

class DisableTwoFactorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token2FA' => ['nullable', 'string', 'size:6'],
            'recovery_code' => ['nullable', 'string'],
        ];
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            $hasCode = $this->filled('token2FA');
            $hasRecoveryCode = $this->filled('recovery_code');

            if ($hasCode === $hasRecoveryCode) {
                $validator->errors()->add('token2FA', 'Envía token2FA o recovery_code (exactamente uno de los dos).');
            }
        });
    }
}
