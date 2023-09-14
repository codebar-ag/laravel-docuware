<?php

namespace CodebarAg\DocuWare\Support;

use CodebarAg\DocuWare\Events\DocuWareCookieCreatedLog;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Exceptions\UnableToLogin;
use CodebarAg\DocuWare\Exceptions\UnableToLoginNoCookies;
use CodebarAg\DocuWare\Requests\Auth\PostLogonRequest;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\HttpFoundation\Response;

class EnsureValidCookie
{
    /**
     * @throws \Throwable
     */
    public static function check(): void
    {
        if (Auth::check()) {
            return;
        }

        EnsureValidCredentials::check();

        event(new DocuWareCookieCreatedLog('Creating new authenticaion cookie for caching'));

        $cookieJar = new CookieJar();

        $request = new PostLogonRequest();

        $request->config()->add('cookies', $cookieJar);

        $response = $request->send();

        event(new DocuWareResponseLog($response));

        throw_if($response->status() === Response::HTTP_UNAUTHORIZED, UnableToLogin::create());
        throw_if($cookieJar->toArray() === [], UnableToLoginNoCookies::create());

        Auth::store($cookieJar);
    }
}
