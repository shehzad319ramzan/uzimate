<?php

namespace App\Models;

use App\Helper\QrCodeHelper;
use App\Relationships\FileRelationship;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use HasFactory, HasUuids, FileRelationship;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'max_sites',
        'spin_after_days',
        'scan_after_hours',
        'use_other_merchant_points',
        'description',
        'status',
        'user_id',
        'merchant_category_id',
        'qr_code',
        'qr_scan_points',
    ];

    public function logo()
    {
        return $this->fileUrl('logo');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(MerchantCategory::class, 'merchant_category_id');
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    /**
     * Full URL for the merchant's QR code image (customers scan this to earn points).
     */
    public function qrCodeImageUrl(): ?string
    {
        if (empty($this->qr_code)) {
            return null;
        }
        return QrCodeHelper::url($this->qr_code);
    }
}
