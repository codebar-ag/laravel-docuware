<?php

namespace CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple;

use CodebarAg\DocuWare\DTO\Documents\DocumentPaginator;
use CodebarAg\DocuWare\Responses\FileCabinets\Search\GetDocumentsFromAFileCabinetResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class Unstaple extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly string $documentTrayId,
        protected readonly string $documentId,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->documentTrayId.'/Operations/ContentDivide';
    }

    public function defaultQuery(): array
    {
        return [
            'DocId' => $this->documentId,
        ];
    }

    public function defaultBody(): array
    {
        return [
            'Operation' => 'Unstaple',
        ];
    }

    public function createDtoFromResponse(Response $response): DocumentPaginator
    {
        return GetDocumentsFromAFileCabinetResponse::fromResponse($response);
    }
}
