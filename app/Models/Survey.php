<?php

namespace App\Models;

use App\Constants\Constants;
use App\Relationships\FileRelationship;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{
    use HasFactory, HasUuids, FileRelationship;

    protected $fillable = [
        'title',
        'description',
        'points',
        'estimated_minutes',
        'merchant_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'estimated_minutes' => 'integer',
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('sort_order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function image(): string
    {
        return $this->fileUrl(Constants::IMAGETYPE);
    }
}
