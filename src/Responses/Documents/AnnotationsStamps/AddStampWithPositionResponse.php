<?php

namespace CodebarAg\DocuWare\Responses\Documents\AnnotationsStamps;

use CodebarAg\DocuWare\DTO\Documents\AnnotationsStamps\Annotations;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Saloon\Http\Response;

final class AddStampWithPositionResponse
{
    public static function fromResponse(Response $response): Collection|Enumerable
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $data = $response->throw()->json('Annotations');

        return collect($data)->map(fn (array $item) => Annotations::fromData($item));
    }
}
