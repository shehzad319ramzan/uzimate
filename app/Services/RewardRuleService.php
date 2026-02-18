<?php

namespace App\Services;

use App\Models\RewardRule;
use Illuminate\Support\Facades\Cache;

/**
 * Reward rules: labels and points per action_type (and optional merchant).
 *
 * Policy when creating customer logs:
 * - Always create the log (full audit trail).
 * - If reward rule for that action is inactive: still create log, but with 0 points (no reward).
 * - Use shouldAwardPointsForAction() / getPointsForAction() to decide points.
 */
class RewardRuleService
{
    private const CACHE_KEY = 'reward_rules_labels';
    private const CACHE_TTL = 3600;

    /**
     * Get display label for action_type. Prefers merchant-specific rule, then global, then default.
     */
    public function getActionTypeLabel(string $actionType, ?string $merchantId = null): string
    {
        $rule = $this->findRuleForAction($actionType, $merchantId);
        if ($rule && $rule->is_active) {
            return $rule->label;
        }
        return RewardRule::defaultActionTypes()[$actionType] ?? ucfirst(str_replace('_', ' ', $actionType));
    }

    /**
     * Find reward rule for action_type (merchant-specific or global, active only).
     */
    public function findRuleForAction(string $actionType, ?string $merchantId = null): ?RewardRule
    {
        $query = RewardRule::where('action_type', $actionType)->where('is_active', true);

        if ($merchantId) {
            $rule = (clone $query)->where('merchant_id', $merchantId)->first();
            if ($rule) {
                return $rule;
            }
        }

        return $query->whereNull('merchant_id')->first();
    }

    /**
     * Get points for action from rule (e.g. for first login). Returns null if no points defined.
     */
    public function getPointsForAction(string $actionType, ?string $merchantId = null): ?int
    {
        $rule = $this->findRuleForAction($actionType, $merchantId);
        return $rule && $rule->points !== null ? (int) $rule->points : null;
    }

    /**
     * Check if rule is "first time only" (award only on first occurrence).
     */
    public function isFirstTimeOnly(string $actionType, ?string $merchantId = null): bool
    {
        $rule = $this->findRuleForAction($actionType, $merchantId);
        return $rule && $rule->trigger_condition === RewardRule::TRIGGER_FIRST_TIME_ONLY;
    }

    /**
     * Whether we should award points for this action (active rule exists).
     * Use when creating customer logs: if false, still create log but with 0 points.
     */
    public function shouldAwardPointsForAction(string $actionType, ?string $merchantId = null): bool
    {
        return $this->findRuleForAction($actionType, $merchantId) !== null;
    }

    /**
     * Clear cache when reward rules are updated (call from observer or after store/update/destroy).
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
