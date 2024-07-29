<?php

namespace CodebarAg\DocuWare\Responses\General\Organization;

use CodebarAg\DocuWare\DTO\General\Organization\FileCabinet;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Saloon\Http\Response;

final class GetAllFileCabinetsAndDocumentTraysResponse
{
    public static function fromResponse(Response $response): Collection|Enumerable
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $cabinets = $response->throw()->json('FileCabinet');

        return collect($cabinets)->map(fn (array $cabinet) => FileCabinet::fromJson($cabinet));
    }
}
