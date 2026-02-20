<?php

namespace App\Dto;

class SurveyDto
{
    public ?string $title;
    public ?string $description;
    public ?int $points;
    public ?int $estimated_minutes;
    public ?string $merchant_id;
    public ?string $status;
    public $file;

    /**
     * Create a new controller instance.
     *
     * @return $request, $model
     */
    public function __construct($request)
    {
        $this->title = isset($request['title']) ? $request['title'] : null;
        $this->description = isset($request['description']) ? $request['description'] : null;
        $this->points = isset($request['points']) ? (int) $request['points'] : null;
        $this->estimated_minutes = isset($request['estimated_minutes']) ? (int) $request['estimated_minutes'] : null;
        $this->merchant_id = ! empty($request['merchant_id']) ? $request['merchant_id'] : null;
        $this->status = isset($request['status']) ? $request['status'] : '1';
        $this->file = isset($request['file']) ? $request['file'] : (isset($request['image']) ? $request['image'] : (request()->file('file') ?? request()->file('image') ?? null));
    }

    public static function fromRequest($request)
    {
        return new self($request);
    }

    public function toArray()
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'points' => $this->points,
            'estimated_minutes' => $this->estimated_minutes,
            'merchant_id' => $this->merchant_id,
            'status' => $this->status,
        ];
    }
}
