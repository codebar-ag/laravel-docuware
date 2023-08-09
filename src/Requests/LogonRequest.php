<?php

namespace CodebarAg\DocuWare\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;
use Saloon\Traits\Body\HasJsonBody;

class LogonRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return '/Account/Logon';
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
