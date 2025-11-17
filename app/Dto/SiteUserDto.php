<?php

namespace App\Dto;

use Illuminate\Http\Request;

class SiteUserDto
{
    public ?string $merchant_id;
    public ?string $site_id;
    public ?string $first_name;
    public ?string $last_name;
    public ?string $email;
    public ?string $phone;
    public ?string $password;
    public ?string $role_id;
    public ?bool $status;
    public $file;

    /**
     * Create a new controller instance.
     *
     * @return $reauest, $modal
     */
    public function __construct($request)
    {
        $payload = $request instanceof Request ? $request->all() : $request;

        $this->merchant_id = !empty($payload['merchant_id']) ? $payload['merchant_id'] : null;
        $this->site_id = !empty($payload['site_id']) ? $payload['site_id'] : null;
        $this->first_name = $payload['first_name'] ?? null;
        $this->last_name = $payload['last_name'] ?? null;
        $this->email = $payload['email'] ?? null;
        $this->phone = $payload['phone'] ?? null;
        $this->password = $payload['password'] ?? null;
        $this->role_id = $payload['role_id'] ?? null;
        $this->status = isset($payload['status']) ? (bool) $payload['status'] : true;
        $this->file = request()->hasFile('file') ? request()->file('file') : null;
    }

    public static function fromRequest($request)
    {
        if (is_array($request)) {
            return new self($request);
        }

        return new self($request);
    }

    public function toArray()
    {
        return [
            'merchant_id' => $this->merchant_id,
            'site_id' => $this->site_id,
            'status' => $this->status ?? true,
        ];
    }

    public function userPayload(): array
    {
        $data = [];

        if ($this->first_name !== null) {
            $data['first_name'] = $this->first_name;
        }

        if ($this->last_name !== null) {
            $data['last_name'] = $this->last_name;
        }

        if ($this->email !== null) {
            $data['email'] = $this->email;
        }

        if ($this->phone !== null) {
            $data['phone'] = $this->phone;
        }

        if ($this->password !== null) {
            $data['password'] = $this->password;
        }

        if ($this->role_id !== null) {
            $data['role_id'] = $this->role_id;
        }

        if ($this->file !== null) {
            $data['image'] = $this->file;
        }

        return $data;
    }
}
