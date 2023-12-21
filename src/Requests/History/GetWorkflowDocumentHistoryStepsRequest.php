<?php

namespace CodebarAg\DocuWare\Requests\History;

use CodebarAg\DocuWare\Responses\History\GetWorkflowDocumentHistoryStepsResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetWorkflowDocumentHistoryStepsRequest extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function __construct(
        public string $workflowGroupId,
        public string $workflowInstanceId
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/Workflows/'.$this->workflowGroupId.'/Instances/'.$this->workflowInstanceId.'/History';
    }

    public function resolveCacheDriver(): LaravelCacheDriver
    {
        return new LaravelCacheDriver(Cache::store(config('laravel-docuware.configurations.cache.driver')));
    }

    public function cacheExpiryInSeconds(): int
    {
        return config('laravel-docuware.configurations.cache.lifetime_in_seconds', 3600);
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return GetWorkflowDocumentHistoryStepsResponse::fromResponse($response);
    }
}
