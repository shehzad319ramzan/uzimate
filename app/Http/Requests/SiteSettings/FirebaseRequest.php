<?php

namespace App\Http\Requests\SiteSettings;

use Illuminate\Foundation\Http\FormRequest;

class FirebaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'firebase_project_id' => ['nullable', 'string', 'max:255'],
            'firebase_credentials' => ['nullable', 'string'],
        ];
    }
}
