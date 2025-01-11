<?php

namespace CodebarAg\DocuWare\Responses\FileCabinets\Upload;

use CodebarAg\DocuWare\DTO\Section;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

final class ReplaceAPDFDocumentSectionResponse
{
    public static function fromResponse(Response $response): Section
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return Section::fromJson($response->throw()->json());
    }
}
