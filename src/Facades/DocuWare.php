<?php

namespace codebar\DocuWare\Facades;

use codebar\DocuWare\DTO\Field;
use codebar\DocuWare\DTO\FileCabinet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @see \codebar\DocuWare\DocuWare
 *
 * @method static string login()
 * @method static void logout()
 * @method static Collection|FileCabinet[] getFileCabinets()
 * @method static Collection|Field[] getFields(string $fileCabinetId)
 */
class DocuWare extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \codebar\DocuWare\DocuWare::class;
    }
}
