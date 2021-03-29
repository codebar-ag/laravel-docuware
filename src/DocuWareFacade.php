<?php

namespace codebar\DocuWare;

use Illuminate\Support\Facades\Facade;

/**
 * @see \codebar\DocuWare\DocuWare
 */
class DocuWareFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-docuware';
    }
}
