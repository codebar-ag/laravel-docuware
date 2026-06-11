<?php

namespace CodebarAg\DocuWare\Concerns;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

/**
 * Shared response prelude for DocuWare response mappers.
 *
 * Fires the response log event, asserts the response is valid (throwing the
 * appropriate typed exception otherwise), and returns the throwing response
 * ready for parsing — the cross-cutting concern every `fromResponse()` shares.
 */
trait HandlesDocuWareResponse
{
    protected static function validated(Response $response): Response
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw();
    }
}
