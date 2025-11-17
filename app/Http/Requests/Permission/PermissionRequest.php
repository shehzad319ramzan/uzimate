<?php

namespace App\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'title' => ['required', 'string', 'max:191'],
            'category' => ['required', 'string', 'max:191'],
        ];
    }
}

