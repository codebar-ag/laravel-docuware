<?php

namespace CodebarAg\DocuWare\Responses\FileCabinets\Dialogs;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\FileCabinets\Dialog;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

final class GetAllDialogsResponse
{
    use HandlesDocuWareResponse;

    /**
     * @return Collection<int, Dialog>
     */
    public static function fromResponse(Response $response): Collection
    {
        $response = self::validated($response);

        $dialogs = $response->throw()->json('Dialog');

        return collect(JsonArrays::listOfRecords($dialogs))->map(fn (array $dialog) => Dialog::fromJson($dialog));
    }
}
