<?php

namespace CodebarAg\DocuWare\DTO\Authentication\OAuth;

use Illuminate\Support\Arr;

final class ResponsibleIdentityService
{
    public static function make(array $data): self
    {
        return new self(
            identityServiceUrl: Arr::get($data, 'IdentityServiceUrl'),
            refreshTokenSupported: Arr::get($data, 'RefreshTokenSupported'),
        );
    }

    public function __construct(
        public string $identityServiceUrl,
        public bool $refreshTokenSupported,
    ) {
    }
}
