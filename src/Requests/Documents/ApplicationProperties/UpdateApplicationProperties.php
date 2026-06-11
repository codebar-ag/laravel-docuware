<?php

namespace CodebarAg\DocuWare\Requests\Documents\ApplicationProperties;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\Responses\Documents\ApplicationProperties\GetApplicationPropertiesResponse;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class UpdateApplicationProperties extends Request implements Cacheable, HasBody
{
    use HasDocuWareCaching;
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  list<array<string, mixed>>  $properties
     */
    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $documentId,
        protected readonly array $properties,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/DocumentApplicationProperties';
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultBody(): array
    {
        return [
            'DocumentApplicationProperty' => $this->properties,
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return GetApplicationPropertiesResponse::fromResponse($response);
    }
}
