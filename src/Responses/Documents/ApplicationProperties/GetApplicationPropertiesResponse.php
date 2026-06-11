<?php

namespace CodebarAg\DocuWare\Responses\Documents\ApplicationProperties;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

final class GetApplicationPropertiesResponse
{
    use HandlesDocuWareResponse;

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public static function fromResponse(Response $response): Collection
    {
        $response = self::validated($response);

        return collect(JsonArrays::listOfRecords($response->throw()->json('DocumentApplicationProperty')));
    }
}
