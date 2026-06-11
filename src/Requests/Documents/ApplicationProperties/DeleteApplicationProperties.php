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

class DeleteApplicationProperties extends Request implements Cacheable, HasBody
{
    use HasDocuWareCaching;
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  list<string>  $propertyNames
     */
    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $documentId,
        protected readonly array $propertyNames,
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
        $props = collect($this->propertyNames)->map(function (string $name) {
            return [
                'Name' => $name,
                'Value' => null,
            ];
        });

        return [
            'DocumentApplicationProperty' => $props,
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return GetApplicationPropertiesResponse::fromResponse($response);
    }
}
