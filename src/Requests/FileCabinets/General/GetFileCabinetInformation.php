<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\General;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\DTO\FileCabinets\General\FileCabinetInformation;
use CodebarAg\DocuWare\Responses\FileCabinets\General\GetFileCabinetInformationResponse;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetFileCabinetInformation extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $fileCabinetId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId;
    }

    public function createDtoFromResponse(Response $response): FileCabinetInformation
    {
        return GetFileCabinetInformationResponse::fromResponse($response);
    }
}
