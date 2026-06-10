<?php

namespace CodebarAg\DocuWare\Requests\Documents\ClipUnclipStapleUnstaple;

use CodebarAg\DocuWare\DTO\Documents\Document;
use CodebarAg\DocuWare\Responses\FileCabinets\Search\GetASpecificDocumentFromAFileCabinetResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class Staple extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  list<array<string, mixed>>  $documents
     */
    public function __construct(
        protected readonly string $documentTrayId,
        protected readonly array $documents,
        protected readonly bool $force = false
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->documentTrayId.'/Operations/ContentMerge';
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultBody(): array
    {
        return [
            'Documents' => $this->documents,
            'Operation' => 'Staple',
            'Force' => $this->force,
        ];
    }

    public function createDtoFromResponse(Response $response): Document
    {
        return GetASpecificDocumentFromAFileCabinetResponse::fromResponse($response);
    }
}
