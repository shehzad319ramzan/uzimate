<?php

namespace App\Dto;

class InboxDto
{
    public readonly string $name;
    public readonly string $status;

    /**
     * Create a new DTO instance.
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->status = $data['status'] ?? '1';
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
        return [
            'name' => $this->name,
            'status' => $this->status,
        ];
    }
}
