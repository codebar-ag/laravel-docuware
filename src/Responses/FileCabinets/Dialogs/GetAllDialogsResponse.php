<?php

namespace CodebarAg\DocuWare\Responses\FileCabinets\Dialogs;

use CodebarAg\DocuWare\DTO\FileCabinets\Dialog;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use CodebarAg\DocuWare\Support\JsonArrays;
use Illuminate\Support\Collection;
use Saloon\Http\Response;

final class GetAllDialogsResponse
{
    /**
     * @return Collection<int, Dialog>
     */
    public static function fromResponse(Response $response): Collection
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $dialogs = $response->throw()->json('Dialog');

        return collect(JsonArrays::listOfRecords($dialogs))->map(fn (array $dialog) => Dialog::fromJson($dialog));
    }
}
