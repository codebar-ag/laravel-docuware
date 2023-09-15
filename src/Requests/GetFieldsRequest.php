<?php

namespace CodebarAg\DocuWare\Requests;

use CodebarAg\DocuWare\DTO\Field;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Facades\Cache;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\CachePlugin\Drivers\LaravelCacheDriver;
use Saloon\CachePlugin\Traits\HasCaching;
use Saloon\Contracts\Response;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetFieldsRequest extends Request implements Cacheable
{
	use HasCaching;

	protected Method $method = Method::GET;

	public function __construct(
		protected readonly string $fileCabinetId
	)
	{
	}

	public function resolveEndpoint(): string
	{
		return '/FileCabinets/' . $this->fileCabinetId;
	}

	public function resolveCacheDriver(): LaravelCacheDriver
	{
		return new LaravelCacheDriver(Cache::store(config('docuware.cache.driver')));
	}

	public function cacheExpiryInSeconds(): int
	{
		return config('docuware.cache.expiry_in_seconds', 3600);
	}

	public function createDtoFromResponse(Response $response): mixed
	{
		event(new DocuWareResponseLog($response));

		EnsureValidResponse::from($response);

		$fields = $response->throw()->json('Fields');

		return collect($fields)->map(fn(array $field) => Field::fromJson($field));
	}
}
