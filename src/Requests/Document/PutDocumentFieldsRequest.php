<?php

namespace CodebarAg\DocuWare\Requests\Document;

use CodebarAg\DocuWare\DTO\DocumentIndex\PrepareDTO;
use CodebarAg\DocuWare\Exceptions\UnableToUpdateFields;
use CodebarAg\DocuWare\Responses\Document\PutDocumentFieldsResponse;
use Illuminate\Support\Collection;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class PutDocumentFieldsRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    /**
     * @throws UnableToUpdateFields
     */
    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $documentId,
        protected readonly ?Collection $indexes = null,
        protected readonly bool $forceUpdate = false,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/Fields';
    }

    public function defaultBody(): array
    {
        $body = [];

        if ($this->indexes) {
            $bodyEncode = json_encode(PrepareDTO::makeField($this->indexes));
            $bodyDecode = json_decode($bodyEncode, true);
            $body = $bodyDecode;
        }

        return $body;

    }

    public function createDtoFromResponse(Response $response): Collection
    {
        return PutDocumentFieldsResponse::fromResponse($response);
    }
}
