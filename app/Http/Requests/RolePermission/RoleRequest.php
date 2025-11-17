<?php

namespace App\Http\Requests\RolePermission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class RoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $title = $this->input('title');
        $slug = $title ? Str::slug($title) : null;

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->where(function ($query) use ($slug) {
                    return $query->where('name', $slug);
                }),
            ],
            'color' => ['required', 'string', 'max:255'],
        ];
    }
}
