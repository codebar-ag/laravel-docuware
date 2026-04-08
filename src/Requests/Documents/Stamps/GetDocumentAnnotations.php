<?php

namespace CodebarAg\DocuWare\Requests\Documents\Stamps;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

/**
 * GET …/FileCabinets/{id}/Documents/{id}/Annotation — list annotations (Postman "Get Annotations").
 */
final class GetDocumentAnnotations extends Request implements Cacheable
{
    use HasCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly int|string $documentId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/Annotation';
    }

    public function resolveCacheDriver(): LaravelCacheDriver
    {
        return new LaravelCacheDriver(Cache::store(config('laravel-docuware.configurations.cache.driver')));
    }

    public function cacheExpiryInSeconds(): int
    {
        return config('laravel-docuware.configurations.cache.lifetime_in_seconds', 3600);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        /** @var mixed $decoded */
        $decoded = $response->throw()->json();

        return collect(self::normalizeAnnotationsList($decoded));
    }

    /**
     * @return list<array<string, mixed>>
     */
    private static function normalizeAnnotationsList(mixed $decoded): array
    {
        if (! is_array($decoded)) {
            return [];
        }

        foreach (['Annotations', 'Annotation', 'Items'] as $key) {
            $nested = Arr::get($decoded, $key);
            if (is_array($nested)) {
                return JsonArrays::listOfRecords($nested);
            }
        }

        if ($decoded === [] || array_is_list($decoded)) {
            return JsonArrays::listOfRecords($decoded);
        }

        return [];
    }
}
