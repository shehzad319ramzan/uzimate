<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return auth()->check();
    }


    protected function prepareForValidation(): void
    {
        if ($this->has('weekdays')) {
            $weekdays = $this->input('weekdays');

            if (is_string($weekdays)) {
                if (trim($weekdays) === '' || $weekdays === 'null') {
                    $this->merge(['weekdays' => null]);
                } elseif ($weekdays === '[]') {
                    $this->merge(['weekdays' => []]);
                } else {
                    $decoded = json_decode($weekdays, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $this->merge(['weekdays' => $decoded]);
                    } else {
                        $this->merge(['weekdays' => null]);
                    }
                }
            } elseif (!is_array($weekdays) && $weekdays !== null) {
                $this->merge(['weekdays' => null]);
            }

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
            'points_to_redeem' => ['nullable', 'integer', 'min:0'],
            'expires_on' => ['nullable', 'date', 'after_or_equal:today'],
            'weekdays' => ['nullable', 'array'],
            'weekdays.*' => ['string', 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun'],
            'description' => ['nullable', 'string', 'max:255'],
            'file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'status' => ['nullable', 'string', 'in:0,1'],
            'send_notification' => ['nullable', 'boolean'],
            'notification_message' => ['nullable', 'string', 'max:500'],
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
