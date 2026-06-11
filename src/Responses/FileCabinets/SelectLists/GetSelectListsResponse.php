<?php

namespace CodebarAg\DocuWare\Responses\FileCabinets\SelectLists;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use Saloon\Http\Response;

final class GetSelectListsResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response): mixed
    {
        $response = self::validated($response);

        return $response->throw()->json('Value');
    }
}
