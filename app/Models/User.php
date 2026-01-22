<?php

namespace App\Models;

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
        'email_verified_at'
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
        ];
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            $user->first_name = Str::title($user->first_name ?? '');
            $user->last_name = Str::title($user->last_name ?? '');
        });

        static::updating(function ($user) {
            $user->first_name = Str::title($user->first_name ?? '');
            $user->last_name = Str::title($user->last_name ?? '');
        });
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
}
