<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\Dialogs;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\Responses\FileCabinets\Dialogs\GetAllDialogsResponse;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetAllDialogs extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Dialogs';
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return GetAllDialogsResponse::fromResponse($response);
    }
}
