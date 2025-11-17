<?php

namespace App\Models;

use App\Relationships\FileRelationship;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Site extends Model
{
    use HasFactory, HasUuids, FileRelationship;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'merchant_id',
        'name',
        'phone',
        'points',
        'address_line_1',
        'address_line_2',
        'city',
        'county',
        'postcode',
        'country',
        'location',
        'coordinates',
        'use_merchant_logo',
        'status'
    ];

    /**
     * Get the merchant that owns the site.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Get the site logo URL.
     */
    public function logo()
    {
        return $this->fileUrl('logo');
    }

    /**
     * Get the logo that should be shown for the site.
     * If merchant logo usage is enabled and the merchant has a logo,
     * prefer that. Otherwise, fall back to the site's custom logo.
     */
    public function displayLogo(): string
    {
        if (($this->use_merchant_logo ?? false) && $this->merchant) {
            $merchantLogo = $this->merchant->logo();

            if (!empty($merchantLogo)) {
                return $merchantLogo;
            }
        }

        return $this->logo();
    }
}
