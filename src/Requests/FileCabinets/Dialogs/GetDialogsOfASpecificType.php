<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\Dialogs;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\Enums\DialogType;
use CodebarAg\DocuWare\Responses\FileCabinets\Dialogs\GetAllDialogsResponse;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetDialogsOfASpecificType extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly DialogType $dialogType,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Dialogs';
    }

    public function defaultQuery(): array
    {
        return [
            'DialogType' => $this->dialogType->value,
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return GetAllDialogsResponse::fromResponse($response);
    }
}
