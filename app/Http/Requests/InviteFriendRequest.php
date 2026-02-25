<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InviteFriendRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'referrer_id' => ['required', 'uuid', 'exists:users,id'],
            'referred_user_id' => ['required', 'uuid', 'exists:users,id'],
            'points_awarded' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ];
    }
}
