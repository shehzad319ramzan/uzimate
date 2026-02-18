<?php

namespace App\Dto\SiteSettings;

class SpinDto
{
    public readonly int $spin_spins_per_day;
    public readonly ?string $spin_default_site_id;
    public readonly int $spin_outcome_nothing;
    public readonly int $spin_outcome_points;
    public readonly int $spin_outcome_offer;
    public readonly int $spin_outcome_discount;
    public readonly int $spin_points_min;
    public readonly int $spin_points_max;
    public readonly int $spin_discount_min;
    public readonly int $spin_discount_max;

    public function __construct(array $request)
    {
        $this->spin_spins_per_day = (int) ($request['spins_per_day'] ?? 1);
        $this->spin_default_site_id = !empty($request['default_site_id']) ? (string) $request['default_site_id'] : null;
        $this->spin_outcome_nothing = (int) ($request['outcome_nothing'] ?? 50);
        $this->spin_outcome_points = (int) ($request['outcome_points'] ?? 30);
        $this->spin_outcome_offer = (int) ($request['outcome_offer'] ?? 15);
        $this->spin_outcome_discount = (int) ($request['outcome_discount'] ?? 5);
        $this->spin_points_min = (int) ($request['points_min'] ?? 25);
        $this->spin_points_max = (int) ($request['points_max'] ?? 100);
        $this->spin_discount_min = (int) ($request['discount_min'] ?? 5);
        $this->spin_discount_max = (int) ($request['discount_max'] ?? 20);
    }

    public static function fromRequest(array $request): self
    {
        return new self($request);
    }

    public function toArray(): array
    {
        return [
            'spin_spins_per_day' => $this->spin_spins_per_day,
            'spin_default_site_id' => $this->spin_default_site_id,
            'spin_outcome_nothing' => $this->spin_outcome_nothing,
            'spin_outcome_points' => $this->spin_outcome_points,
            'spin_outcome_offer' => $this->spin_outcome_offer,
            'spin_outcome_discount' => $this->spin_outcome_discount,
            'spin_points_min' => $this->spin_points_min,
            'spin_points_max' => $this->spin_points_max,
            'spin_discount_min' => $this->spin_discount_min,
            'spin_discount_max' => $this->spin_discount_max,
        ];
    }
}
