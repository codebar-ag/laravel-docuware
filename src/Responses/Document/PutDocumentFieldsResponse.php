<?php

namespace CodebarAg\DocuWare\Responses\Document;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use CodebarAg\DocuWare\Support\ParseValue;
use Illuminate\Support\Collection;
use Saloon\Contracts\Response;

final class PutDocumentFieldsResponse
{
    public static function fromResponse(Response $response): Collection
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $fields = $response->throw()->json('Field');

        return collect($fields)->mapWithKeys(function (array $field) {
            return [
                $field['FieldName'] => ParseValue::field($field),
            ];
        });
    }
}
