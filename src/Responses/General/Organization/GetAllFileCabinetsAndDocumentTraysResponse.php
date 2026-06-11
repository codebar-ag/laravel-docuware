<?php

namespace CodebarAg\DocuWare\Responses\General\Organization;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\General\Organization\FileCabinet;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

final class GetAllFileCabinetsAndDocumentTraysResponse
{
    use HandlesDocuWareResponse;

    /**
     * @return Collection<int, FileCabinet>
     */
    public static function fromResponse(Response $response): Collection
    {
        $response = self::validated($response);

        $cabinets = $response->throw()->json('FileCabinet');

        return collect(JsonArrays::listOfRecords($cabinets))->map(fn (array $cabinet) => FileCabinet::fromJson($cabinet));
    }
}
