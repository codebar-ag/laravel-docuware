<?php

namespace CodebarAg\DocuWare\Requests\Documents\ApplicationProperties;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\Responses\Documents\ApplicationProperties\GetApplicationPropertiesResponse;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetApplicationProperties extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $documentId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/DocumentApplicationProperties';
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return GetApplicationPropertiesResponse::fromResponse($response);
    }
}
