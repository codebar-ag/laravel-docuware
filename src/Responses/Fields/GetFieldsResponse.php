<?php

namespace CodebarAg\DocuWare\Responses\Fields;

use CodebarAg\DocuWare\DTO\Documents\Field;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

final class GetFieldsResponse
{
    /**
     * @return Collection<int, Field>
     */
    public static function fromResponse(Response $response): Collection
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $fields = $response->throw()->json('Fields');

        return collect(JsonArrays::listOfRecords($fields))->map(fn (array $field) => Field::fromJson($field));
    }
}
