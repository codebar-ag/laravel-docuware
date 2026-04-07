<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\CheckInCheckOut;

use CodebarAg\DocuWare\DTO\FileCabinets\CheckoutToFileSystemResult;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

final class CheckoutDocumentToFileSystem extends Request
{
    protected Method $method = Method::POST;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly int|string $documentId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/CheckoutToFileSystem';
    }

    public function createDtoFromResponse(Response $response): CheckoutToFileSystemResult
    {
        return CheckoutToFileSystemResult::fromResponse($response);
    }
}
