<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\General;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\Responses\FileCabinets\General\GetTotalNumberOfDocumentsResponse;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetTotalNumberOfDocuments extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $searchDialogId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Query/CountExpression';
    }

    public function defaultQuery(): array
    {
        return [
            'DialogId' => $this->searchDialogId,
        ];
    }

    public function createDtoFromResponse(Response $response): int
    {
        return GetTotalNumberOfDocumentsResponse::fromResponse($response);
    }
}
