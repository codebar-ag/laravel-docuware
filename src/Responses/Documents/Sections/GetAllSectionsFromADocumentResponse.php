<?php

namespace CodebarAg\DocuWare\Responses\Documents\Sections;

use CodebarAg\DocuWare\DTO\Section;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

final class GetAllSectionsFromADocumentResponse
{
    /**
     * @return Collection<int, Section>
     */
    public static function fromResponse(Response $response): Collection
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return collect(JsonArrays::listOfRecords($response->throw()->json('Section')))
            ->map(fn (array $section) => Section::fromJson($section));
    }
}
