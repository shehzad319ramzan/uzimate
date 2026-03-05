<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationCampaign extends Model
{
    protected $fillable = [
        'type', 'name', 'message', 'channels',
        'target_type', 'target_config',
        'scheduled_at', 'sent_at', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'channels' => 'array',
            'target_config' => 'array',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
