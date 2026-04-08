<?php

namespace CodebarAg\DocuWare\Requests\Documents\UpdateIndexValues;

use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDateTimeDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexDecimalDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexKeywordDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexMemoDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexNumericDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTableDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\PrepareDTO;
use CodebarAg\DocuWare\Responses\Documents\UpdateIndexValues\UpdateIndexValuesResponse;
use Illuminate\Support\Collection;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class UpdateIndexValues extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    /**
     * @param  Collection<int, IndexTextDTO|IndexDateDTO|IndexDateTimeDTO|IndexNumericDTO|IndexDecimalDTO|IndexTableDTO|IndexKeywordDTO|IndexMemoDTO>|null  $indexes
     */
    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $documentId,
        protected readonly ?Collection $indexes = null,
        protected readonly bool $forceUpdate = false,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/Fields';
    }

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @return Collection<string, mixed>
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        return UpdateIndexValuesResponse::fromResponse($response);
    }
}
