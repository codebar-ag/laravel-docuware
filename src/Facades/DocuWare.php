<?php

namespace CodebarAg\DocuWare\Facades;

use Carbon\Carbon;
use CodebarAg\DocuWare\DocuWareSearch;
use CodebarAg\DocuWare\DocuWareUrl;
use CodebarAg\DocuWare\DTO\Dialog;
use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\DTO\Field;
use CodebarAg\DocuWare\DTO\FileCabinet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @see \CodebarAg\DocuWare\DocuWare
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
 * @method static null|int|float|Carbon|string updateDocumentValue(string $fileCabinetId, int $documentId, string $fieldName, string $newValue)
 * @method static Document uploadDocument(string $fileCabinetId, string $fileContent, string $fileName, ?Collection $indexes = null)
 * @method static void deleteDocument(string $fileCabinetId, int $documentId)
 * @method static DocuWareSearch search()
 * @method static DocuWareUrl url()
 */
class DocuWare extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \CodebarAg\DocuWare\DocuWare::class;
    }
}
