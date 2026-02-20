<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SurveySubmitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'points_earned' => (int) ($this->resource['points_earned'] ?? 0),
            'survey_id' => $this->resource['survey_id'] ?? null,
        ];
    }
}
