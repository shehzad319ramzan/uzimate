<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert weekdays JSON string to array if it's a string
        if ($this->has('weekdays')) {
            $weekdays = $this->input('weekdays');
            
            if (is_string($weekdays)) {
                // Handle empty string
                if (trim($weekdays) === '' || $weekdays === 'null') {
                    $this->merge(['weekdays' => null]);
                } elseif ($weekdays === '[]') {
                    // Empty array JSON string should be converted to empty array
                    $this->merge(['weekdays' => []]);
                } else {
                    $decoded = json_decode($weekdays, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $this->merge(['weekdays' => $decoded]);
                    } else {
                        // If JSON decode fails, set to null
                        $this->merge(['weekdays' => null]);
                    }
                }
            } elseif (!is_array($weekdays) && $weekdays !== null) {
                // If weekdays exists but is not array, string, or null, set to null
                $this->merge(['weekdays' => null]);
            }
            // If it's already an array or null, leave it as is
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'merchant_id' => ['nullable', 'uuid', 'exists:merchants,id'],
            'site_id' => ['required', 'uuid', 'exists:sites,id'],
            'title' => ['required', 'string', 'max:255'],
            'points_required' => ['required', 'integer', 'min:0'],
            'expires_on' => ['nullable', 'date', 'after_or_equal:today'],
            'weekdays' => ['nullable', 'array'],
            'weekdays.*' => ['string', 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun'],
            'description' => ['nullable', 'string', 'max:255'],
            'file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'status' => ['nullable', 'string', 'in:0,1'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'site_id.required' => 'Site is required',
            'title.required' => 'Title is required',
            'points_required.required' => 'Points Required is required',
        ];
    }
}
