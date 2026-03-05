<?php

namespace App\Dto\SiteSettings;

class FirebaseDto
{
    public readonly ?string $firebase_project_id;
    public readonly ?string $firebase_credentials;

    public function __construct(array $request)
    {
        $this->firebase_project_id = $request['firebase_project_id'] ?? null;
        $this->firebase_credentials = $request['firebase_credentials'] ?? null;
    }

    public static function fromRequest($request): self
    {
        return new self(is_array($request) ? $request : $request->all());
    }

    public function toArray(): array
    {
        return [
            'firebase_project_id' => $this->firebase_project_id,
            'firebase_credentials' => $this->firebase_credentials,
        ];
    }
}
