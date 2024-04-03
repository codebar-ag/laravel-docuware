<?php

namespace CodebarAg\DocuWare;

use CodebarAg\DocuWare\DTO\Cookie;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Auth\GetLogoffRequest;
use CodebarAg\DocuWare\Requests\Auth\PostLoginRequest;
use CodebarAg\DocuWare\Support\Auth;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use CodebarAg\DocuWare\Support\EnsureValidCredentials;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use GuzzleHttp\Cookie\CookieJar;
use Saloon\Exceptions\InvalidResponseClassException;
use Saloon\Exceptions\PendingRequestException;

class DocuWare
{
    /**
     * @throws InvalidResponseClassException
     * @throws \Throwable
     * @throws \ReflectionException
     * @throws PendingRequestException
     */
    public function cookie(string $url, string $username, string $password, $rememberMe = false, $redirectToMyselfInCaseOfError = false, $licenseType = null): Cookie
    {
        $request = new PostLoginRequest(
            $url,
            $username,
            $password,
            $rememberMe,
            $redirectToMyselfInCaseOfError,
            $licenseType
        );

        $response = $request->send();

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $cookies = collect($response->headers()->get('Set-Cookie'))->flatMap(function ($cookie) {
            $data = explode(';', $cookie)[0];
            $split = explode('=', $data);

            return [$split[0] => $split[1]];
        });

        return Cookie::make(CookieJar::fromArray($cookies->toArray(), $url));
    }

    /**
     * @throws InvalidResponseClassException
     * @throws \Throwable
     * @throws \ReflectionException
     * @throws PendingRequestException
     */
    public function login(): void
    {
        EnsureValidCredentials::check();
        // Checks if already logged in, if not, logs in
        EnsureValidCookie::check();
    }

    /**
     * @throws InvalidResponseClassException
     * @throws \Throwable
     * @throws \ReflectionException
     * @throws PendingRequestException
     */
    public function logout(): void
    {
        // SoloRequest
        $request = new GetLogoffRequest();

        $response = $request->send();

        event(new DocuWareResponseLog($response));

        Auth::forget();

        $response->throw();
    }

    public function searchRequestBuilder(): DocuWareSearchRequestBuilder
    {
        return new DocuWareSearchRequestBuilder();
    }

    public function url(): DocuWareUrl
    {
        return new DocuWareUrl();
    }
}
