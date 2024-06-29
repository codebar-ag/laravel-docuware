<?php

namespace CodebarAg\DocuWare\Requests\General\Organization;

use CodebarAg\DocuWare\Responses\General\Organization\RequestLoginTokenResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class GetLoginToken extends Request implements Cacheable, HasBody
{
    use HasCaching;
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public array $targetProducts = ['PlatformService'],
        public string $usage = 'Multi',
        public string $lifetime = '1.00:00:00',
    ) {}

    public function resolveEndpoint(): string
    {
        return '/Organization/LoginToken';
    }

    protected function defaultBody(): array
    {
        return [
            'TargetProducts' => $this->targetProducts,
            'Usage' => $this->usage,
            'Lifetime' => $this->lifetime,
        ];
    }

    public function resolveCacheDriver(): LaravelCacheDriver
    {
        return new LaravelCacheDriver(Cache::store(config('laravel-docuware.configurations.cache.driver')));
    }

    public function cacheExpiryInSeconds(): int
    {
        return config('laravel-docuware.configurations.cache.lifetime_in_seconds', 3600);
    }

    public function createDtoFromResponse(Response $response): string
    {
        return RequestLoginTokenResponse::fromResponse($response);
    }
}
