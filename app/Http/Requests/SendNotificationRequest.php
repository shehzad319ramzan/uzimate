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
            'inactive_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'send_to_all_inactive' => ['nullable', 'boolean'],
            'send_to_all' => ['nullable', 'boolean'],
            'send_to_all_today' => ['nullable', 'boolean'],
            'offer_id' => ['nullable', 'exists:offers,id'],
        ];
        return $rules;
    }
}
