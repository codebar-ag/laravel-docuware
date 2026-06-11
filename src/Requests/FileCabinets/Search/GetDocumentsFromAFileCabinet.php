<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\Search;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\DTO\Documents\DocumentPaginator;
use CodebarAg\DocuWare\Responses\FileCabinets\Search\GetDocumentsFromAFileCabinetResponse;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetDocumentsFromAFileCabinet extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    /**
     * @param  list<string>  $fields
     */
    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly array $fields = [],
        protected readonly int $page = 1,
        protected readonly int $perPage = 50,
    ) {}

    public function defaultQuery(): array
    {
        return [
            'fields' => filled($this->fields) ? implode(',', $this->fields) : null,
            'count' => $this->perPage,
            'start' => ($this->page - 1) * $this->perPage,
        ];
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents';
    }

    public function createDtoFromResponse(Response $response): DocumentPaginator
    {
        return GetDocumentsFromAFileCabinetResponse::fromResponse($response, $this->page, $this->perPage);
    }
}
