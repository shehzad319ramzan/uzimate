<?php

namespace App\Http\Requests;

use App\Models\SiteUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SiteUserRequest extends FormRequest
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
        $siteUserId = $this->route('site');
        $siteUser = $siteUserId ? SiteUser::with('user')->find($siteUserId) : null;
        $userId = $siteUser?->user_id;

        $passwordRules = ['nullable', 'confirmed', 'min:8'];
        if ($this->isMethod('post')) {
            $passwordRules[0] = 'required';
        }

        return [
            'merchant_id' => ['required', 'uuid', 'exists:merchants,id'],
            'site_id' => [
                'required',
                'uuid',
                Rule::exists('sites', 'id')->where(function ($query) {
                    $merchantId = $this->input('merchant_id');
                    if ($merchantId) {
                        $query->where('merchant_id', $merchantId);
                    }
                }),
            ],
            'first_name' => ['required', 'string', 'max:150'],
            'last_name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => $passwordRules,
            'file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }
}
