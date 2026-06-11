<?php

namespace CodebarAg\DocuWare\Requests\Documents\Sections;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\Responses\Documents\Sections\DeleteSectionResponse;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class DeleteSection extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::DELETE;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $sectionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Sections/'.$this->sectionId;
    }

    public function createDtoFromResponse(Response $response): bool
    {
        return DeleteSectionResponse::fromResponse($response);
    }
}
