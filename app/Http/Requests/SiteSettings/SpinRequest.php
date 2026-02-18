<?php

namespace App\Http\Requests\SiteSettings;

use Illuminate\Foundation\Http\FormRequest;

class SpinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'spins_per_day' => ['required', 'integer', 'min:1', 'max:999'],
            'default_site_id' => ['nullable', 'string', 'exists:sites,id'],
            'outcome_nothing' => ['required', 'integer', 'min:0', 'max:100'],
            'outcome_points' => ['required', 'integer', 'min:0', 'max:100'],
            'outcome_offer' => ['required', 'integer', 'min:0', 'max:100'],
            'outcome_discount' => ['required', 'integer', 'min:0', 'max:100'],
            'points_min' => ['required', 'integer', 'min:0', 'max:99999'],
            'points_max' => ['required', 'integer', 'min:0', 'max:99999', 'gte:points_min'],
            'discount_min' => ['required', 'integer', 'min:0', 'max:100'],
            'discount_max' => ['required', 'integer', 'min:0', 'max:100', 'gte:discount_min'],
        ];
    }

    public function messages(): array
    {
        return [
            'outcome_nothing.required' => 'Outcome chance for "Nothing" is required.',
            'outcome_points.required' => 'Outcome chance for "Points" is required.',
            'outcome_offer.required' => 'Outcome chance for "Offer" is required.',
            'outcome_discount.required' => 'Outcome chance for "Discount" is required.',
            'points_max.gte' => 'Points max must be greater than or equal to points min.',
            'discount_max.gte' => 'Discount max must be greater than or equal to discount min.',
        ];
    }
}
