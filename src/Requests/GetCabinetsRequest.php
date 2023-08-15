<?php

namespace CodebarAg\DocuWare\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetCabinetsRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/FileCabinets';
    }
}
