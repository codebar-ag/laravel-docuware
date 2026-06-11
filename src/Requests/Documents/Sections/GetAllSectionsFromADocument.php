<?php

namespace CodebarAg\DocuWare\Requests\Documents\Sections;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\DTO\Section;
use CodebarAg\DocuWare\Responses\Documents\Sections\GetAllSectionsFromADocumentResponse;
use Illuminate\Support\Collection;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetAllSectionsFromADocument extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $documentId
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Sections';
    }

    public function defaultQuery(): array
    {
        return [
            'docid' => $this->documentId,
        ];
    }

    /**
     * @return Collection<int, Section>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return GetAllSectionsFromADocumentResponse::fromResponse($response);
    }
}
