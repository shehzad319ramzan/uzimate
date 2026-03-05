<?php

namespace App\Http\Requests;

use App\Models\NotificationSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NotificationSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $type = $this->route('type', $this->input('type'));
        $rules = [
            'type' => ['required', Rule::in(array_keys(NotificationSetting::TYPES))],
            'is_active' => ['nullable', 'boolean'],
        ];

        if (in_array($type, [NotificationSetting::TYPE_MISS_YOU, 'miss_you'], true)) {
            $rules['inactive_days'] = ['required', 'integer', 'min:1', 'max:365'];
            $rules['message_template'] = ['nullable', 'string', 'max:1000'];
            $rules['channels'] = ['nullable', 'array'];
            $rules['channels.*'] = [Rule::in(array_keys(NotificationSetting::CHANNELS))];
        }

        if (in_array($type, [NotificationSetting::TYPE_BIRTHDAY, 'birthday'], true)) {
            $rules['message_template'] = ['nullable', 'string', 'max:1000'];
            $rules['reward_points'] = ['required', 'integer', 'min:0', 'max:100000'];
            $rules['channels'] = ['nullable', 'array'];
            $rules['channels.*'] = [Rule::in(array_keys(NotificationSetting::CHANNELS))];
        }

        if (in_array($type, [NotificationSetting::TYPE_SPECIAL_OFFER, 'special_offer'], true)) {
            $rules['message_template'] = ['nullable', 'string', 'max:1000'];
            $rules['channels'] = ['nullable', 'array'];
            $rules['channels.*'] = [Rule::in(array_keys(NotificationSetting::CHANNELS))];
        }

        return $rules;
    }
}
