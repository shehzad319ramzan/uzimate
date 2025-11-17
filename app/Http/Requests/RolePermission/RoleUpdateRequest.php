<?php

namespace App\Http\Requests\RolePermission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class RoleUpdateRequest extends FormRequest
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
        $title = $this->input('title');
        $slug = $title ? Str::slug($title) : null;
        $role = $this->route('role');
        $roleId = is_object($role) ? $role->id : $role;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->ignore($roleId)
                    ->where(function ($query) use ($slug) {
                        return $query->where('name', $slug);
                    }),
            ],
            'color' => ['required', 'string', 'max:255'],
        ];
    }
}
