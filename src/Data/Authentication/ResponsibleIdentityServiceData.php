<?php

namespace CodebarAg\DocuWare\Data\Authentication;

use CodebarAg\DocuWare\Data\DocuWareData;
use Illuminate\Support\Arr;

final class ResponsibleIdentityServiceData extends DocuWareData
{
    public function __construct(
        public ?string $identityServiceUrl,
        public bool $refreshTokenSupported,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromDocuWare(array $data): self
    {
        $url = Arr::get($data, 'IdentityServiceUrl');

        return new self(
            identityServiceUrl: is_string($url) && $url !== '' ? $url : null,
            refreshTokenSupported: filter_var(
                Arr::get($data, 'RefreshTokenSupported'),
                FILTER_VALIDATE_BOOLEAN,
            ),
        );
    }
}
