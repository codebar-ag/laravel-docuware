<?php

namespace CodebarAg\DocuWare\Requests\Document;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Exceptions\UnableToDownloadDocuments;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Arr;
use Saloon\Contracts\Response;
use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetDocumentsDownloadRequest extends Request
{
    protected Method $method = Method::GET;

    protected readonly string $documentId;

    public function __construct(
        protected readonly string $fileCabinetId,
        protected array $documentIds,
    ) {
        throw_if(
            count($documentIds) < 2,
            UnableToDownloadDocuments::selectAtLeastTwoDocuments(),
        );

        $this->documentId = Arr::get($documentIds, 0);
        $this->documentIds = array_slice($documentIds, 1);
    }

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/FileDownload';
    }

    public function defaultQuery(): array
    {
        return [
            'keepAnnotations' => 'false',
            'append' => implode(',', $this->documentIds),
        ];
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->body();
    }
}
