<?php

namespace CodebarAg\DocuWare\Requests\Documents\Sections;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\DTO\Section;
use CodebarAg\DocuWare\Responses\Documents\Sections\GetASecificSectionResponse;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetASpecificSection extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $sectionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Sections/'.$this->sectionId;
    }

    public function createDtoFromResponse(Response $response): Section
    {
        return GetASecificSectionResponse::fromResponse($response);
    }
}
