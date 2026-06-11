<?php

namespace CodebarAg\DocuWare\Responses\FileCabinets\General;

use CodebarAg\DocuWare\Concerns\HandlesDocuWareResponse;
use CodebarAg\DocuWare\DTO\FileCabinets\General\FileCabinetInformation;
use Saloon\Http\Response;

final class GetFileCabinetInformationResponse
{
    use HandlesDocuWareResponse;

    public static function fromResponse(Response $response): FileCabinetInformation
    {
        $response = self::validated($response);

        $cabinet = $response->throw()->json();

        return FileCabinetInformation::fromJson($cabinet);
    }
}
