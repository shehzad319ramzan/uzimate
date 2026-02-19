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
            'outcome_offer' => ['nullable', 'integer', 'min:0', 'max:100'],
            'outcome_discount' => ['nullable', 'integer', 'min:0', 'max:100'],
            'points_min' => ['required', 'integer', 'min:0', 'max:99999'],
            'points_max' => ['required', 'integer', 'min:0', 'max:99999', 'gte:points_min'],
            'discount_min' => ['nullable', 'integer', 'min:0', 'max:100'],
            'discount_max' => ['nullable', 'integer', 'min:0', 'max:100', 'gte:discount_min'],
        ];
    }

    public function messages(): array
    {
        return [
            'outcome_nothing.required' => 'Outcome chance for "Nothing" is required.',
            'outcome_points.required' => 'Outcome chance for "Points" is required.',
            'points_max.gte' => 'Points max must be greater than or equal to points min.',
            'discount_max.gte' => 'Discount max must be greater than or equal to discount min.',
        ];
    }

    /**
     * Configure the validator so Nothing + Points sum to 100 (spin awards only points).
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $nothing = (int) $this->input('outcome_nothing', 0);
            $points = (int) $this->input('outcome_points', 0);
            if ($nothing + $points !== 100) {
                $validator->errors()->add(
                    'outcome_points',
                    'Nothing and Points must add up to 100 (e.g. 50 + 50).'
                );
            }
        });
    }
}
