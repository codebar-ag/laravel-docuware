<?php

namespace CodebarAg\DocuWare\Responses\Documents\UpdateIndexValues;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\Support\JsonArrays;
use CodebarAg\DocuWare\Support\ParseValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

final class UpdateIndexValuesResponse
{
    use HandlesDocuWareResponse;

    /**
     * @return Collection<string, mixed>
     */
    public static function fromResponse(Response $response): Collection
    {
        $response = self::validated($response);

        $fields = $response->throw()->json('Field');

        return collect(JsonArrays::listOfRecords($fields))
            ->filter(fn (array $field) => is_string(Arr::get($field, 'FieldName')) && Arr::get($field, 'FieldName') !== '')
            ->mapWithKeys(fn (array $field) => [
                Arr::get($field, 'FieldName') => ParseValue::field($field),
            ]);
    }
}
