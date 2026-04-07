<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\CheckInCheckOut;

use CodebarAg\DocuWare\DTO\Documents\Document;
use CodebarAg\DocuWare\Responses\FileCabinets\Search\GetASpecificDocumentFromAFileCabinetResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Data\MultipartValue;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasMultipartBody;

/**
 * Check-in a checked-out document. Postman uses multipart fields "CheckIn" (JSON file) and "File[]" (binary).
 */
final class CheckInDocumentFromFileSystem extends Request implements HasBody
{
    use HasMultipartBody;

    protected Method $method = Method::POST;

    /**
     * @param  string  $checkInJson  JSON for CheckIn metadata (e.g. DocumentVersion, Comments)
     */
    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly int|string $documentId,
        protected readonly string $checkInJson,
        protected readonly string $fileContent,
        protected readonly string $fileName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/CheckInFromFileSystem';
    }

    /**
     * @return list<MultipartValue>
     */
    protected function defaultBody(): array
    {
        return [
            new MultipartValue(name: 'CheckIn', value: $this->checkInJson, filename: 'CheckIn.json'),
            new MultipartValue(name: 'File[]', value: $this->fileContent, filename: $this->fileName),
        ];
    }

    public function createDtoFromResponse(Response $response): Document
    {
        return GetASpecificDocumentFromAFileCabinetResponse::fromResponse($response);
    }
}
