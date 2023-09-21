<?php

namespace CodebarAg\DocuWare\Responses\Dialogs;

use CodebarAg\DocuWare\DTO\Dialog;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Contracts\Response;

final class GetDialogResponse
{
    public static function fromResponse(Response $response): Dialog
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $dialog = $response->throw()->json();

        ray($dialog);

        return Dialog::fromJson($dialog);
    }
}
