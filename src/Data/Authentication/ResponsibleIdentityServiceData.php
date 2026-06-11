<?php

namespace CodebarAg\DocuWare\Data\Authentication;

use CodebarAg\DocuWare\Data\DocuWareData;
use Illuminate\Support\Arr;

final class ResponsibleIdentityServiceData extends DocuWareData
{
    public function __construct(
        public string $identityServiceUrl,
        public bool $refreshTokenSupported,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        return new self(
            identityServiceUrl: Arr::get($data, 'IdentityServiceUrl'),
            refreshTokenSupported: Arr::get($data, 'RefreshTokenSupported'),
        );
    }
}
