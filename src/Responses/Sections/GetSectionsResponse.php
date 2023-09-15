<?php

namespace CodebarAg\DocuWare\Responses\Sections;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Saloon\Contracts\Response;

final class GetSectionsResponse
{
    public static function fromResponse(Response $response): Collection|Enumerable
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->json('Sections');
    }
}
