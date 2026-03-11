<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
    protected function prepareForValidation(): void
    {
        $merge = [];
        if ($this->hasFile('profile')) {
            $merge['file'] = $this->file('profile');
        }
        if ($this->hasFile('image')) {
            $merge['file'] = $merge['file'] ?? $this->file('image');
        }
        if ($merge !== []) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        $userId = auth()->id();
        $currentEmail = auth()->user()->email;

        return [
            'f_name' => ['required', 'string', 'max:100'],
            'l_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:200', function ($attribute, $value, $fail) use ($currentEmail) {
                if ($value !== $currentEmail) {
                    $fail('You cannot change your email. Please contact the admin!');
                }
            }],
            'about' => ['nullable','string', 'max:250'],
            'date_of_birth' => ['nullable', 'date'],
            'file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'profile' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
        ];
    }
}
