<?php

namespace CodebarAg\DocuWare\Responses\FileCabinets\Dialogs;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\FileCabinets\Dialog;
use Saloon\Http\Response;

final class GetASpecificDialogResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response): Dialog
    {
        $response = self::validated($response);

        $dialog = $response->throw()->json();

        return Dialog::fromJson($dialog);
    }
}
