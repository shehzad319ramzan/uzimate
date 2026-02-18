<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CustomerLog extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'merchant_id',
        'site_id',
        'offer_id',
        'user_id',
        'action_type',
        'action_category',
        'description',
        'points_affected',
        'points_balance_before',
        'points_balance_after',
        'related_model_type',
        'related_model_id',
        'metadata',
        'performed_by_id',
        'ip_address',
        'user_agent',
        'location_data',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'points_affected' => 'integer',
            'points_balance_before' => 'integer',
            'points_balance_after' => 'integer',
            'metadata' => 'array',
            'location_data' => 'array',
        ];
    }

    /**
     * Get the merchant that owns the log.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Get the site that owns the log.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the offer (when log is for offer scan).
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * Get the user (customer) who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who performed the action (admin/staff).
     */
    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_id');
    }

    /**
     * Get the related model (polymorphic).
     */
    public function relatedModel(): MorphTo
    {
        return $this->morphTo('related_model', 'related_model_type', 'related_model_id');
    }
}
