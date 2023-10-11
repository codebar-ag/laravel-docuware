<?php

namespace CodebarAg\DocuWare\Requests\Auth;

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Saloon\Http\SoloRequest;
use Saloon\Traits\Body\HasFormBody;

class PostLoginRequest extends SoloRequest implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected readonly string $url,
        protected readonly string $username,
        protected readonly string $password,
        protected readonly bool $rememberMe = false,
        protected readonly bool $redirectToMyselfInCaseOfError = false,
        protected readonly ?string $licenseType = null,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return $this->url.'/DocuWare/Platform/Account/Logon';
    }

    protected function defaultBody(): array
    {
        return [
            'UserName' => $this->username,
            'Password' => $this->password,
            'RememberMe' => $this->rememberMe,
            'RedirectToMyselfInCaseOfError' => $this->redirectToMyselfInCaseOfError,
            'LicenseType' => $this->licenseType,
        ];
    }

    public function createDtoFromResponse(Response $response): Response
    {
        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response;
    }
}
