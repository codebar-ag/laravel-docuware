<?php

namespace CodebarAg\DocuWare\Requests\Authentication\OAuth;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\SoloRequest;

class GetIdentityServiceConfiguration extends SoloRequest implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        public string $identityServiceUrl,
    ) {}

    public function resolveEndpoint(): string
    {
        return $this->identityServiceUrl.'/.well-known/openid-configuration';
    }

    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }
}
