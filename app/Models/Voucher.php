<?php

namespace App\Models;

use App\Constants\Constants;
use App\Relationships\FileRelationship;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Voucher extends Model
{
    use HasFactory, HasUuids, FileRelationship;

    protected $fillable = [
        'merchant_id',
        'title',
        'description',
        'terms_and_conditions',
        'points_required',
        'valid_until',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'points_required' => 'integer',
            'valid_until' => 'date',
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(Offer::class, 'offer_voucher');
    }

    public function image(): string
    {
        $own = $this->fileUrl(Constants::IMAGETYPE);
        if ($own !== '') {
            return $own;
        }
        $offers = $this->relationLoaded('offers') ? $this->offers : $this->offers()->get();
        $first = $offers->first();
        return $first ? ($first->image() ?: '') : '';
    }

    public function scopeActive($query)
    {
        return $query->where('status', '1');
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('valid_until')->orWhere('valid_until', '>=', now()->toDateString());
        });
    }
}
