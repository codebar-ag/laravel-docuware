<?php

namespace CodebarAg\DocuWare\Responses\FileCabinets\SelectLists;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

final class GetSelectListsResponse
{
    public static function fromResponse(Response $response): mixed
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->json('Value');
    }
}
