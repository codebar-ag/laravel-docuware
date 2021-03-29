<?php

namespace codebar\DocuWare\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \codebar\DocuWare\DocuWare
 *
 * @method static string hello()
 */
class DocuWare extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \codebar\DocuWare\DocuWare::class;
    }
}
