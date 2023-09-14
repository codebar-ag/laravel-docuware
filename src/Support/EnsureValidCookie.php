<?php

namespace CodebarAg\DocuWare\Support;

use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\Events\DocuWareAuthenticateLog;
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
            event(new DocuWareAuthenticateLog('Authenticating with cached credentials'));
            return;
        }

        EnsureValidCredentials::check();

        event(new DocuWareAuthenticateLog('Authenticating with credentials'));

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
