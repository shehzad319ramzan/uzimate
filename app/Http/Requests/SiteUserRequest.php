<?php

namespace App\Http\Requests;

use App\Constants\Constants;
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

    protected function isSuperMode(): bool
    {
        if ($this->routeIs('siteusers.store-super')) {
            return true;
        }

        $siteUser = $this->currentSiteUser();

        return $siteUser?->user?->hasRole(Constants::SUPERADMIN) ?? false;
    }

    protected function currentSiteUser(): ?SiteUser
    {
        $siteUserId = $this->route('site');

        if (!$siteUserId) {
            return null;
        }

        return SiteUser::with('user.roles')->find($siteUserId);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $siteUser = $this->currentSiteUser();
        $userId = $siteUser?->user_id;

        $passwordRules = ['nullable', 'confirmed', 'min:8'];
        if ($this->isMethod('post')) {
            $passwordRules[0] = 'required';
        }

        $merchantRules = ['required', 'uuid', 'exists:merchants,id'];
        $siteRules = [
            'required',
            'array',
            'min:1',
        ];

        $siteItemRules = [
            'uuid',
            Rule::exists('sites', 'id')->where(function ($query) {
                $merchantId = $this->input('merchant_id');
                if ($merchantId) {
                    $query->where('merchant_id', $merchantId);
                }
            }),
        ];
        $roleRules = ['required', 'exists:roles,id'];

        if ($this->isSuperMode()) {
            $merchantRules = ['nullable', 'uuid', 'exists:merchants,id'];
            $siteRules = ['nullable', 'array'];
            $siteItemRules = ['nullable', 'uuid', 'exists:sites,id'];
            $roleRules = ['nullable', 'exists:roles,id'];
        }

        return [
            'merchant_id' => $merchantRules,
            'site_ids' => $siteRules,
            'site_ids.*' => $siteItemRules,
            'first_name' => ['required', 'string', 'max:150'],
            'last_name' => ['required', 'string', 'max:150'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'role_id' => $roleRules,
            'password' => $passwordRules,
            'file' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }
}
