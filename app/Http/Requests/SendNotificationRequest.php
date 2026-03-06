<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $rules = [
            'message' => ['nullable', 'string', 'max:2000'],
            'title' => ['nullable', 'string', 'max:255'],
            'channels' => ['required', 'array', 'min:1'],
            'channels.*' => ['in:email,push'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'inactive_days' => [
                'nullable',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value === null || $value === '') {
                        return;
                    }
                    if ($value === 'all') {
                        return;
                    }
                    if (! is_numeric($value) || (int) $value < 1 || (int) $value > 365) {
                        $fail('The inactive days must be an integer between 1 and 365, or "all".');
                    }
                },
            ],
            'send_to_all_inactive' => ['nullable', 'boolean'],
            'send_to_all' => ['nullable', 'boolean'],
            'send_to_all_today' => ['nullable', 'boolean'],
            'birthday_filter' => ['nullable', 'string', 'max:10'],
            'offer_id' => ['nullable', 'exists:offers,id'],
        ];
        return $rules;
    }
}
