<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $rules = [
            'merchant_id' => ['required', 'uuid', 'exists:merchants,id'],
            'offer_ids' => ['nullable', 'array'],
            'offer_ids.*' => ['uuid', 'exists:offers,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'terms_and_conditions' => ['nullable', 'string'],
            'points_required' => ['required', 'integer', 'min:0', 'max:999999'],
            'valid_until' => ['nullable', 'date'],
            'status' => ['required', 'string', 'in:0,1'],
        ];
        if ($this->isMethod('post') || $this->hasFile('file')) {
            $rules['file'] = ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'];
        }
        return $rules;
    }
}
