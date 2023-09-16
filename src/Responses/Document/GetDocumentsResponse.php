<?php

namespace CodebarAg\DocuWare\Responses\Document;

use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Collection;
use Saloon\Contracts\Response;

final class GetDocumentsResponse
{
    public static function fromResponse(Response $response): Collection
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $items = $response->throw()->json('Items');

        return collect($items)->map(fn (array $item) => Document::fromJson($item));
    }
}
