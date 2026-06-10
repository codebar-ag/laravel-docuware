<?php

namespace CodebarAg\DocuWare\Facades;

use CodebarAg\DocuWare\DocuWareSearchRequestBuilder;
use CodebarAg\DocuWare\DocuWareUrl;
use Illuminate\Support\Facades\Facade;

/**
 * @see \CodebarAg\DocuWare\DocuWare
 *
 * Low-level requests are sent through a DocuWareConnector:
 * `$connector->send(new SomeRequest(...))->dto();`. This facade only exposes the two
 * helpers that genuinely live on the DocuWare class.
 *
 * @method static DocuWareSearchRequestBuilder searchRequestBuilder()
 * @method static DocuWareUrl url(string $url, string $username, string $password, ?string $passphrase = null)
 */
class DocuWare extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \CodebarAg\DocuWare\DocuWare::class;
    }
}
