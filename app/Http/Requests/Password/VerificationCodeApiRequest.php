<?php

namespace App\Http\Requests\Password;

use Illuminate\Foundation\Http\FormRequest;

class VerificationCodeApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return !auth()->check();
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'digits:4'],
        ];
    }
}
