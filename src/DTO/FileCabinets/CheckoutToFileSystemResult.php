<?php

namespace CodebarAg\DocuWare\DTO\FileCabinets;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

/**
 * Result of checking a document out to the file system.
 *
 * DocuWare returns the document's file content (so it can be edited locally while the
 * document stays locked), not a JSON envelope — hence this captures the raw body and its
 * content type rather than parsing JSON.
 */
final class CheckoutToFileSystemResult
{
    public function __construct(
        public readonly string $content,
        public readonly ?string $contentType,
    ) {}

    public static function fromResponse(Response $response): self
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $response = $response->throw();

        return new self(
            content: $response->body(),
            contentType: $response->header('Content-Type'),
        );
    }
}
