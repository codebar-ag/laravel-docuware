<?php

namespace CodebarAg\DocuWare\Requests\FileCabinets\Batch;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

/**
 * POST /FileCabinets/{id}/Operations/BatchDocumentsUpdateFields
 *
 * Body shape matches DocuWare Postman examples (BatchUpdateDocumentsSource, BatchUpdateDialogExpressionSource, or keyword batch).
 *
 * @phpstan-type BatchBody array<string, mixed>
 */
final class BatchDocumentsUpdateFields extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        protected readonly string $fileCabinetId,
        protected readonly array $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/FileCabinets/'.$this->fileCabinetId.'/Operations/BatchDocumentsUpdateFields';
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
