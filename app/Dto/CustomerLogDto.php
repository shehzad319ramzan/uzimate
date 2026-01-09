<?php

namespace App\Dto;

class CustomerLogDto
{
    public readonly ?string $merchant_id;
    public readonly ?string $site_id;
    public readonly int $user_id;
    public readonly string $action_type;
    public readonly string $action_category;
    public readonly string $description;
    public readonly ?int $points_affected;
    public readonly ?int $points_balance_before;
    public readonly ?int $points_balance_after;
    public readonly ?string $related_model_type;
    public readonly ?string $related_model_id;
    public readonly ?array $metadata;
    public readonly ?int $performed_by_id;
    public readonly ?string $ip_address;
    public readonly ?string $user_agent;
    public readonly ?array $location_data;

    /**
     * Create a new DTO instance.
     */
    public function __construct(array $data)
    {
        $this->merchant_id = $data['merchant_id'] ?? null;
        $this->site_id = $data['site_id'] ?? null;
        $this->user_id = (int) $data['user_id'];
        $this->action_type = $data['action_type'];
        $this->action_category = $data['action_category'] ?? 'system';
        $this->description = $data['description'];
        $this->points_affected = isset($data['points_affected']) ? (int) $data['points_affected'] : null;
        $this->points_balance_before = isset($data['points_balance_before']) ? (int) $data['points_balance_before'] : null;
        $this->points_balance_after = isset($data['points_balance_after']) ? (int) $data['points_balance_after'] : null;
        $this->related_model_type = $data['related_model_type'] ?? null;
        $this->related_model_id = $data['related_model_id'] ?? null;
        $this->metadata = isset($data['metadata']) ? (is_array($data['metadata']) ? $data['metadata'] : json_decode($data['metadata'], true)) : null;
        $this->performed_by_id = isset($data['performed_by_id']) ? (int) $data['performed_by_id'] : (auth()->check() ? auth()->id() : null);
        $this->ip_address = $data['ip_address'] ?? request()->ip();
        $this->user_agent = $data['user_agent'] ?? request()->userAgent();
        $this->location_data = isset($data['location_data']) ? (is_array($data['location_data']) ? $data['location_data'] : json_decode($data['location_data'], true)) : null;
    }

    public static function fromRequest($request): self
    {
        if (is_array($request)) {
            return new self($request);
        }

        return new self($request?->all() ?? []);
    }

    public function toArray(): array
    {
        $data = [
            'user_id' => $this->user_id,
            'action_type' => $this->action_type,
            'action_category' => $this->action_category,
            'description' => $this->description,
            'performed_by_id' => $this->performed_by_id,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
        ];

        if ($this->merchant_id !== null) {
            $data['merchant_id'] = $this->merchant_id;
        }

        if ($this->site_id !== null) {
            $data['site_id'] = $this->site_id;
        }

        if ($this->points_affected !== null) {
            $data['points_affected'] = $this->points_affected;
        }

        if ($this->points_balance_before !== null) {
            $data['points_balance_before'] = $this->points_balance_before;
        }

        if ($this->points_balance_after !== null) {
            $data['points_balance_after'] = $this->points_balance_after;
        }

        if ($this->related_model_type !== null) {
            $data['related_model_type'] = $this->related_model_type;
        }

        if ($this->related_model_id !== null) {
            $data['related_model_id'] = $this->related_model_id;
        }

        if ($this->metadata !== null) {
            $data['metadata'] = $this->metadata;
        }

        if ($this->location_data !== null) {
            $data['location_data'] = $this->location_data;
        }

        return $data;
    }
}
