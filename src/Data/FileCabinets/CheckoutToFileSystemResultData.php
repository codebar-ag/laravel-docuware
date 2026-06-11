<?php

namespace CodebarAg\DocuWare\Data\FileCabinets;

use CodebarAg\DocuWare\Data\DocuWareData;
use Saloon\Http\Response;

/**
 * Result of checking a document out to the file system.
 *
 * DocuWare returns the document's file content (so it can be edited locally while the
 * document stays locked), not a JSON envelope — hence this captures the raw body and its
 * content type rather than parsing JSON.
 */
final class CheckoutToFileSystemResultData extends DocuWareData
{
    public function __construct(
        public string $content,
        public ?string $contentType,
    ) {}

    public static function fromResponse(Response $response): self
    {
        return new self(
            content: (string) $response->body(),
            contentType: $response->header('Content-Type'),
        );
    }
}
