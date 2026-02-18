<?php

namespace App\Http\Requests;

use App\Models\RewardRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RewardRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $merchantId = $this->input('merchant_id');
        $unique = Rule::unique('reward_rules', 'action_type')
            ->where(function ($q) use ($merchantId) {
                if ($merchantId === null || $merchantId === '') {
                    $q->whereNull('merchant_id');
                } else {
                    $q->where('merchant_id', $merchantId);
                }
            });

        if ($id = $this->route('id')) {
            $unique->ignore($id);
        }

        return [
            'merchant_id' => ['nullable', 'uuid', 'exists:merchants,id'],
            'action_type' => ['required', 'string', 'max:100', $unique],
            'label' => ['required', 'string', 'max:255'],
            'points' => ['nullable', 'integer', 'min:0'],
            'trigger_condition' => ['required', 'in:' . implode(',', array_keys(RewardRule::triggerConditions()))],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('is_active')) {
            $this->merge(['is_active' => false]);
        }
    }
}
