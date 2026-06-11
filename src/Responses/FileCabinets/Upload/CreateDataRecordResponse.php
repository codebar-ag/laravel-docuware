<?php

namespace CodebarAg\DocuWare\Responses\FileCabinets\Upload;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\Documents\Document;
use Saloon\Http\Response;

final class CreateDataRecordResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response): Document
    {
        $response = self::validated($response);

        $data = $response->throw()->json();

        return Document::fromJson($data);
    }
}
