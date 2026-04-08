<?php

namespace CodebarAg\DocuWare\Responses\Documents\ApplicationProperties;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

final class GetApplicationPropertiesResponse
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public static function fromResponse(Response $response): Collection
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return collect(JsonArrays::listOfRecords($response->throw()->json('DocumentApplicationProperty')));
    }
}
