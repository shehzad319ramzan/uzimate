<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvitedFriendResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $invite = $this->resource;
        $friend = $invite->referredUser;

        return [
            'id' => $invite->id,
            'friend_name' => $friend ? (trim(($friend->first_name ?? '') . ' ' . ($friend->last_name ?? '')) ?: ($friend->email ?? '')) : '',
            'friend_email' => $friend?->email ?? null,
            'joined_at' => $invite->created_at?->format('M d, Y'),
            'points_awarded' => (int) ($invite->points_awarded ?? 0),
        ];
    }
}
