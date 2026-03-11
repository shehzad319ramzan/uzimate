<?php

namespace App\Models;

use App\Models\InviteFriend;
use App\Relationships\FileRelationship;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles, FileRelationship;

    protected $fillable = [
        'first_name',
        'last_name',
        'about',
        'email',
        'phone',
        'date_of_birth',
        'password',
        'provider_id',
        'provider',
        'email_verified_at',
        'referral_code',
        'fcm_token',
        'push_notifications_enabled',
        'email_notifications_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
            'push_notifications_enabled' => 'boolean',
            'email_notifications_enabled' => 'boolean',
        ];
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            $user->first_name = Str::title($user->first_name ?? '');
            $user->last_name = Str::title($user->last_name ?? '');
            if (empty($user->referral_code)) {
                $user->referral_code = self::generateUniqueReferralCode();
            }
        });

        static::updating(function ($user) {
            $user->first_name = Str::title($user->first_name ?? '');
            $user->last_name = Str::title($user->last_name ?? '');
        });
    }

    public static function generateUniqueReferralCode(): string
    {
        do {
            $code = (string) random_int(1000000, 9999999);
        } while (self::where('referral_code', $code)->exists());
        return $code;
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<InviteFriend> */
    public function referredFriends()
    {
        return $this->hasMany(InviteFriend::class, 'referrer_id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasOne<InviteFriend> */
    public function referredBy()
    {
        return $this->hasOne(InviteFriend::class, 'referred_user_id');
    }

    public function getFullNameAttribute(): string
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }

    public function profile()
    {
        return $this->fileUrl('profile');
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\HasMany<CustomerLog> */
    public function customerLogs()
    {
        return $this->hasMany(CustomerLog::class);
    }
}
