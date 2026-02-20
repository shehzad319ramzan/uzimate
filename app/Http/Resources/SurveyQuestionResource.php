<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SurveyQuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question_text' => $this->question_text,
            'sort_order' => (int) $this->sort_order,
            'options' => SurveyQuestionOptionResource::collection($this->whenLoaded('options')),
        ];
    }
}
