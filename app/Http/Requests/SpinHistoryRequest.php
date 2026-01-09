<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpinHistoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'site_id' => ['required', 'string', 'exists:sites,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'spin_result_type' => ['required', 'string', 'in:points,offer,nothing,discount'],
            'points_earned' => ['nullable', 'integer', 'min:0'],
            'offer_id' => ['nullable', 'string', 'exists:offers,id'],
            'reward_value' => ['nullable', 'numeric', 'min:0'],
            'spin_number' => ['nullable', 'integer', 'min:1'],
            'is_eligible' => ['nullable', 'boolean'],
            'last_spin_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'ip_address' => ['nullable', 'ip'],
            'device_info' => ['nullable', 'json'],
        ];

        // If spin_result_type is 'points', points_earned is required
        if (request()->input('spin_result_type') === 'points') {
            $rules['points_earned'] = ['required', 'integer', 'min:1'];
        }

        // If spin_result_type is 'offer', offer_id is required
        if (request()->input('spin_result_type') === 'offer') {
            $rules['offer_id'] = ['required', 'string', 'exists:offers,id'];
        }

        return $rules;
    }
}
