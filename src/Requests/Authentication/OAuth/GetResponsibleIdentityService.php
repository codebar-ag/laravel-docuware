<?php

namespace CodebarAg\DocuWare\Requests\Authentication\OAuth;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\DTO\Authentication\OAuth\ResponsibleIdentityService;
use CodebarAg\DocuWare\Responses\Authentication\OAuth\GetResponsibleIdentityServiceResponse;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Http\SoloRequest;

class GetResponsibleIdentityService extends SoloRequest implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected ?string $url = null
    ) {}

    public function resolveEndpoint(): string
    {
        $url = $this->url ?? config('laravel-docuware.credentials.url');
        $platform = trim(config('laravel-docuware.platform_path', 'DocuWare/Platform'), '/');
        $base = rtrim($url, '/').'/'.$platform;

        return $base.'/Home/IdentityServiceInfo';
    }

    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    public function createDtoFromResponse(Response $response): ResponsibleIdentityService
    {
        return GetResponsibleIdentityServiceResponse::fromResponse($response);
    }
}
