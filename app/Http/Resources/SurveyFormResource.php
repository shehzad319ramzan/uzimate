<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SurveyFormResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'survey_id' => $this->id,
            'title' => $this->title,
            'questions' => SurveyQuestionResource::collection($this->whenLoaded('questions')),
        ];
    }
}
