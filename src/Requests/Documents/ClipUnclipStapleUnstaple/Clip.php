<?php

namespace CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple;

use CodebarAg\DocuWare\DTO\Documents\Document;
use CodebarAg\DocuWare\Responses\FileCabinets\Search\GetASpecificDocumentFromAFileCabinetResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class Clip extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly string $documentTrayId,
        protected readonly array $documents,
        protected readonly bool $force = false
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->documentTrayId.'/Operations/ContentMerge';
    }

    public function defaultBody(): array
    {
        return [
            'Documents' => $this->documents,
            'Operation' => 'Clip',
            'Force' => $this->force,
        ];
    }

    public function createDtoFromResponse(Response $response): Document
    {
        return GetASpecificDocumentFromAFileCabinetResponse::fromResponse($response);
    }
}
