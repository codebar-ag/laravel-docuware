<?php

namespace CodebarAg\DocuWare\Responses\FileCabinets\Dialogs;

use CodebarAg\DocuWare\DTO\FileCabinets\Dialog;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Saloon\Http\Response;

final class GetAllDialogsResponse
{
    public static function fromResponse(Response $response): Collection|Enumerable
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $dialogs = $response->throw()->json('Dialog');

        return collect($dialogs)->map(fn (array $dialog) => Dialog::fromJson($dialog));
    }
}
