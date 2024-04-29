<?php

namespace CodebarAg\DocuWare\Responses\FileCabinets\General;

use CodebarAg\DocuWare\DTO\FileCabinets\General\FileCabinetInformation;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Http\Response;

final class GetFileCabinetInformationResponse
{
    public static function fromResponse(Response $response): FileCabinetInformation
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $cabinet = $response->throw()->json();

        return FileCabinetInformation::fromJson($cabinet);
    }
}
