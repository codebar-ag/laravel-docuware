<?php

namespace CodebarAg\DocuWare\Responses\SelectList;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Contracts\Response;

final class GetSelectListResponse
{
    public static function fromResponse(Response $response): mixed
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->json('Value');
    }
}
