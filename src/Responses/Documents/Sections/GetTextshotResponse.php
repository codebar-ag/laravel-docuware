<?php

namespace CodebarAg\DocuWare\Responses\Documents\Sections;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\Textshot;
use Saloon\Http\Response;

final class GetTextshotResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response): Textshot
    {
        $response = self::validated($response);

        return Textshot::fromJson($response->throw()->json());
    }
}
