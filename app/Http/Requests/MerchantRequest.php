<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantRequest extends FormRequest
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
        // dd(request()->all());
        return [
            'merchant_name' => ['required', 'string', 'max:255'],
            'merchant_category_id' => ['nullable', 'exists:merchant_categories,id'],
            'max_sites' => ['required', 'integer', 'min:1'],
            'spin_after_days' => ['nullable', 'integer', 'min:0'],
            'scan_after_hours' => ['nullable', 'integer', 'min:0'],
            'use_other_merchant_points' => ['nullable', 'boolean'],
            'qr_scan_points' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'description' => ['nullable', 'string'],
            'file' => ['nullable', 'image'],
        ];
    }
}
