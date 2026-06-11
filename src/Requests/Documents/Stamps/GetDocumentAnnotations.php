<?php

namespace CodebarAg\DocuWare\Requests\Documents\Stamps;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * GET …/FileCabinets/{id}/Documents/{id}/Annotation — list annotations (Postman "Get Annotations").
 */
final class GetDocumentAnnotations extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly int|string $documentId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/Annotation';
    }
}
