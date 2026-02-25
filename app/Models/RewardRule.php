<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardRule extends Model
{
    use HasFactory, HasUuids;

    public const TRIGGER_EVERY_TIME = 'every_time';
    public const TRIGGER_FIRST_TIME_ONLY = 'first_time_only';

    protected $fillable = [
        'merchant_id',
        'action_type',
        'label',
        'points',
        'trigger_condition',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public static function triggerConditions(): array
    {
        return [
            self::TRIGGER_EVERY_TIME => 'Every time',
            self::TRIGGER_FIRST_TIME_ONLY => 'First time only',
        ];
    }

    public static function defaultActionTypes(): array
    {
        return [
            'point_earned' => 'Point Earned',
            'point_redeemed' => 'Point Redeemed',
            'point_expired' => 'Point Expired',
            'point_adjusted' => 'Point Adjusted',
            'spin_completed' => 'Spin Completed',
            'survey_completed' => 'Survey Completed',
            'offer_viewed' => 'Offer Viewed',
            'offer_redeemed' => 'Offer Redeemed',
            'qr_code_scanned' => 'QR Code Scanned',
            'check_in' => 'Check In',
            'profile_updated' => 'Profile Updated',
            'login' => 'Login',
            'logout' => 'Logout',
            'account_created' => 'Account Created',
            'referral_completed' => 'Referral Completed',
            'custom' => 'Custom',
        ];
    }
}
