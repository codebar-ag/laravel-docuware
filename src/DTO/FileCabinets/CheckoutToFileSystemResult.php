<?php

namespace CodebarAg\DocuWare\DTO\FileCabinets;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use CodebarAg\DocuWare\Support\JsonArrays;
use Saloon\Http\Response;

final class CheckoutToFileSystemResult
{
    /**
     * @param  list<array<string, mixed>>  $links
     */
    public function __construct(
        public array $links,
    ) {}

    public static function fromResponse(Response $response): self
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $raw = $response->throw()->json('Links');

        return new self(JsonArrays::listOfRecords($raw));
    }
}
