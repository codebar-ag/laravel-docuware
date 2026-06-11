<?php

namespace CodebarAg\DocuWare\Requests\Documents\Download;

use CodebarAg\DocuWare\Concerns\HasDocuWareCaching;
use CodebarAg\DocuWare\Enums\TargetFileType;
use CodebarAg\DocuWare\Responses\Documents\Download\DownloadDocumentResponse;
use Saloon\CachePlugin\Contracts\Cacheable;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class DownloadDocument extends Request implements Cacheable
{
    use HasDocuWareCaching;

    protected Method $method = Method::GET;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly string $documentId,
        protected readonly TargetFileType $targetFileType = TargetFileType::AUTO,
        protected readonly bool $keepAnnotations = false,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/FileDownload';
    }

    /**
     * @return array<string, string>
     */
    public function defaultQuery(): array
    {
        return [
            'targetFileType' => $this->targetFileType->value,
            'keepAnnotations' => $this->keepAnnotations ? 'true' : 'false',
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return DownloadDocumentResponse::fromResponse($response);
    }
}
