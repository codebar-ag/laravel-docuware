<?php

namespace CodebarAg\DocuWare\Responses\Documents\Sections;

use CodebarAg\DocuWare\DTO\Section;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Saloon\Http\Response;

final class GetAllSectionsFromADocumentResponse
{
    public static function fromResponse(Response $response): Collection|Enumerable
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return collect($response->throw()->json('Section'))->map(fn ($section) => Section::fromJson($section));
    }
}
