<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'points' => ['required', 'integer', 'min:0', 'max:99999'],
            'estimated_minutes' => ['required', 'integer', 'min:1', 'max:60'],
            'merchant_id' => ['nullable', 'uuid', 'exists:merchants,id'],
            'status' => ['required', 'string', 'in:0,1'],
            'questions' => ['nullable', 'array'],
            'questions.*.question_text' => ['nullable', 'string', 'max:1000'],
            'questions.*.options' => ['nullable', 'array'],
            'questions.*.options.*' => ['nullable', 'string', 'max:500'],
        ];

        if ($this->isMethod('post') || $this->hasFile('file')) {
            $rules['file'] = ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'];
        }

        return $rules;
    }
}
