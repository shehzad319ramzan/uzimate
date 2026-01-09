<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerLogRequest extends FormRequest
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
        $actionTypes = [
            'point_earned', 'point_redeemed', 'point_expired', 'point_adjusted',
            'spin_completed',
            'offer_viewed', 'offer_redeemed',
            'qr_code_scanned', 'check_in',
            'profile_updated', 'login', 'logout', 'account_created',
            'custom'
        ];

        $actionCategories = ['points', 'spins', 'offers', 'scans', 'profile', 'system'];

        return [
            'site_id' => ['nullable', 'string', 'exists:sites,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'action_type' => ['required', 'string', 'in:' . implode(',', $actionTypes)],
            'action_category' => ['nullable', 'string', 'in:' . implode(',', $actionCategories)],
            'description' => ['required', 'string', 'max:1000'],
            'points_affected' => ['nullable', 'integer'],
            'points_balance_before' => ['nullable', 'integer', 'min:0'],
            'points_balance_after' => ['nullable', 'integer', 'min:0'],
            'related_model_type' => ['nullable', 'string', 'max:255'],
            'related_model_id' => ['nullable', 'string'],
            'metadata' => ['nullable', 'json'],
            'performed_by_id' => ['nullable', 'integer', 'exists:users,id'],
            'ip_address' => ['nullable', 'ip'],
            'user_agent' => ['nullable', 'string', 'max:500'],
            'location_data' => ['nullable', 'json'],
        ];
    }
}
