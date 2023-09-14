<?php

namespace CodebarAg\DocuWare\Requests\Auth;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\SoloRequest;
use Saloon\Traits\Body\HasFormBody;

class PostLogonRequest extends SoloRequest implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return config('docuware.credentials.url').'/DocuWare/Platform/Account/Logon';
    }

    protected function defaultBody(): array
    {
        return [
            'UserName' => config('docuware.credentials.username'),
            'Password' => config('docuware.credentials.password'),
            'RememberMe' => false,
            'RedirectToMyselfInCaseOfError' => false,
            'LicenseType' => null,
        ];
    }
}
