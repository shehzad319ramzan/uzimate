<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class SurveyResource extends JsonResource
{
    /**
     * Whether to truncate description (e.g. for list view).
     */
    public static bool $truncateDescription = false;

    public function toArray(Request $request): array
    {
        $description = $this->description;
        if (static::$truncateDescription && $description) {
            $description = Str::limit($description, 100);
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $description,
            'points' => (int) $this->points,
            'estimated_minutes' => (int) $this->estimated_minutes,
            'image_url' => $this->image() ?: null,
            'merchant' => $this->whenLoaded('merchant', function () {
                return [
                    'id' => $this->merchant->id,
                    'name' => $this->merchant->name,
                    'image_url' => $this->merchant->logo() ?: null,
                ];
            }),
        ];
    }
}
