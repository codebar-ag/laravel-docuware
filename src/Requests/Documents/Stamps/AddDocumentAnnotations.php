<?php

namespace CodebarAg\DocuWare\Requests\Documents\Stamps;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

/**
 * POST …/Documents/{id}/Annotation — stamps and other annotations (Postman "Add Stamp With Position/Best Position").
 */
final class AddDocumentAnnotations extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  array<string, mixed>  $payload  e.g. ["Annotations" => [...]]
     */
    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly int|string $documentId,
        protected readonly array $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Documents/'.$this->documentId.'/Annotation';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->payload;
    }

    /**
     * @return array<string, mixed>
     */
    public function createDtoFromResponse(Response $response): array
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        /** @var array<string, mixed> $json */
        $json = $response->throw()->json();

        return $json;
    }
}
