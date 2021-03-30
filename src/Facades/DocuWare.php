<?php

namespace codebar\DocuWare\Facades;

use codebar\DocuWare\DTO\Dialog;
use codebar\DocuWare\DTO\Document;
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
 * @method static Collection|Dialog[] getDialogs(string $fileCabinetId)
 * @method static array getSelectList(string $fileCabinetId, string $dialogId, string $fieldName)
 * @method static Document getDocument(string $fileCabinetId, int $documentId)
 * @method static string getDocumentPreview(string $fileCabinetId, int $documentId)
 * @method static string downloadDocument(string $fileCabinetId, int $documentId)
 * @method static string downloadDocuments(string $fileCabinetId, array $documentIds)
 */
class DocuWare extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \codebar\DocuWare\DocuWare::class;
    }
}
