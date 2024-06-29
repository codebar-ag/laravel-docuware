<?php

namespace CodebarAg\DocuWare\Responses\Documents\Sections;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use Saloon\Http\Response;

final class DeleteSectionResponse
{
    public static function fromResponse(Response $response): bool
    {
        event(new DocuWareResponseLog($response));

        return $response->status() === 200;
    }
}
