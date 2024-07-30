<?php

namespace CodebarAg\DocuWare\Responses\FileCabinets\Dialogs;

use CodebarAg\DocuWare\DTO\FileCabinets\Dialog;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

final class GetASpecificDialogResponse
{
    public static function fromResponse(Response $response): Dialog
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $dialog = $response->throw()->json();

        return Dialog::fromJson($dialog);
    }
}
