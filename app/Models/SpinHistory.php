<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpinHistory extends Model
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
        'user_id',
        'spin_result_type',
        'points_earned',
        'offer_id',
        'reward_value',
        'spin_number',
        'is_eligible',
        'last_spin_date',
        'notes',
        'ip_address',
        'device_info',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'points_earned' => 'integer',
            'reward_value' => 'decimal:2',
            'spin_number' => 'integer',
            'is_eligible' => 'boolean',
            'last_spin_date' => 'date',
            'device_info' => 'array',
        ];
    }

    /**
     * Get the merchant that owns the spin history.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Get the site that owns the spin history.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the user who performed the spin.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the offer that was won (if applicable).
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }
}
