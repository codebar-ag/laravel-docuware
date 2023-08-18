<?php

namespace CodebarAg\DocuWare\Requests\Auth;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetLogoffRequest extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/Account/Logoff';
    }
}
