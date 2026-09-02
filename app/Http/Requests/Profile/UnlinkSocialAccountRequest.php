<?php

namespace App\Http\Requests\Profile;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnlinkSocialAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider' => ['required', 'string', Rule::in(['google'])],
            'password' => ['nullable', 'string'],
            'token2FA' => ['nullable', 'string', 'size:6'],
            'recovery_code' => ['nullable', 'string'],
        ];
    }

    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {
            if ($this->user()->has_usable_password && ! $this->filled('password')) {
                $validator->errors()->add('password', 'La contraseña es obligatoria.');
            }

            if (! $this->user()->two_factor_status) {
                return;
            }

            $hasCode = $this->filled('token2FA');
            $hasRecoveryCode = $this->filled('recovery_code');

            if ($hasCode === $hasRecoveryCode) {
                $validator->errors()->add('token2FA', 'Envía token2FA o recovery_code (exactamente uno de los dos).');
            }
        });
    }
}
