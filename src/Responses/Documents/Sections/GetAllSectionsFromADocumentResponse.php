<?php

namespace CodebarAg\DocuWare\Responses\Documents\Sections;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\Section;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

final class GetAllSectionsFromADocumentResponse
{
    use HandlesDocuWareResponse;

    /**
     * @return Collection<int, Section>
     */
    public static function fromResponse(Response $response): Collection
    {
        $response = self::validated($response);

        return collect(JsonArrays::listOfRecords($response->throw()->json('Section')))
            ->map(fn (array $section) => Section::fromJson($section));
    }
}
