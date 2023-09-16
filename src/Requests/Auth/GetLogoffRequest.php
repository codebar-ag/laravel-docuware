<?php

namespace CodebarAg\DocuWare\Requests\Auth;

use CodebarAg\DocuWare\Support\EnsureValidCookie;
use Saloon\Enums\Method;
use Saloon\Http\SoloRequest;

class GetLogoffRequest extends SoloRequest
{
    protected Method $method = Method::GET;

    public function __construct()
    {
        EnsureValidCookie::check();
    }

    public function resolveEndpoint(): string
    {
        return config('docuware.credentials.url').'/DocuWare/Platform/Account/Logoff';
    }
}
