<?php

namespace App\Models;

use App\Constants\Constants;
use App\Relationships\FileRelationship;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    use HasFactory, HasUuids, FileRelationship;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'merchant_id',
        'site_id',
        'title',
        'points_required',
        'expires_on',
        'weekdays',
        'description',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_on' => 'date',
            'weekdays' => 'array',
        ];
    }

    /**
     * Get the merchant that owns the offer.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Get the site that owns the offer.
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the offer image URL.
     */
    public function image()
    {
        return $this->fileUrl(Constants::IMAGETYPE);
    }
}
