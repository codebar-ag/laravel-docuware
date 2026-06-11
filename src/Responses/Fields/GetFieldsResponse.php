<?php

namespace CodebarAg\DocuWare\Responses\Fields;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\Documents\Field;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

final class GetFieldsResponse
{
    use HandlesDocuWareResponse;

    /**
     * @return Collection<int, Field>
     */
    public static function fromResponse(Response $response): Collection
    {
        $response = self::validated($response);

        $fields = $response->throw()->json('Fields');

        return collect(JsonArrays::listOfRecords($fields))->map(fn (array $field) => Field::fromJson($field));
    }
}
