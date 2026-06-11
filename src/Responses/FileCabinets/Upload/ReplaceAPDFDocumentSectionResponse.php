<?php

namespace CodebarAg\DocuWare\Responses\FileCabinets\Upload;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\Section;
use Saloon\Http\Response;

final class ReplaceAPDFDocumentSectionResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response): Section
    {
        $response = self::validated($response);

        return Section::fromJson($response->throw()->json());
    }
}
