<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationSetting extends Model
{
    protected $fillable = ['type', 'name', 'config', 'is_active'];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public const TYPE_MISS_YOU = 'miss_you';
    public const TYPE_SPECIAL_DAY = 'special_day';
    public const TYPE_SPECIAL_OFFER = 'special_offer';
    public const TYPE_BIRTHDAY = 'birthday';

    public const TYPES = [
        self::TYPE_MISS_YOU => 'Miss You',
        self::TYPE_SPECIAL_DAY => 'Special Day',
        self::TYPE_SPECIAL_OFFER => 'Special Offer',
        self::TYPE_BIRTHDAY => 'Birthday',
    ];

    public const CHANNEL_EMAIL = 'email';
    public const CHANNEL_PUSH = 'push';
    public const CHANNELS = ['email' => 'Email', 'push' => 'Mobile Push'];

    public function getConfigValue(string $key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }

    public function setConfigValue(string $key, $value): void
    {
        $config = $this->config ?? [];
        data_set($config, $key, $value);
        $this->config = $config;
        $this->save();
    }
}
