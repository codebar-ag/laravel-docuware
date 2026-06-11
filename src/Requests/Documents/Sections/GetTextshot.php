<?php

namespace CodebarAg\DocuWare\Requests\Documents\Sections;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\Responses\Documents\Sections\GetTextshotResponse;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetTextshot extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $sectionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Sections/'.$this->sectionId.'/Textshot';
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return GetTextshotResponse::fromResponse($response);
    }
}
