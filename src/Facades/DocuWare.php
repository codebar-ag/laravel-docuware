<?php

namespace CodebarAg\DocuWare\Facades;

use Carbon\Carbon;
use CodebarAg\DocuWare\DocuWareSearchRequestBuilder;
use CodebarAg\DocuWare\DocuWareUrl;
use CodebarAg\DocuWare\DTO\Dialog;
use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\DTO\DocumentThumbnail;
use CodebarAg\DocuWare\DTO\Field;
use CodebarAg\DocuWare\DTO\FileCabinet;
use CodebarAg\DocuWare\DTO\Organization;
use CodebarAg\DocuWare\DTO\OrganizationIndex;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @see \CodebarAg\DocuWare\DocuWare
 *
 * @method static self cookie()
 * @method static string login()
 * @method static void logout()
 * @method static Organization getOrganization(string $organizationId)
 * @method static Collection|OrganizationIndex[] getOrganizations()
 * @method static Collection|FileCabinet[] getFileCabinets()
 * @method static Collection|Field[] getFields(string $fileCabinetId)
 * @method static Collection|Dialog[] getDialogs(string $fileCabinetId)
 * @method static array getSelectList(string $fileCabinetId, string $dialogId, string $fieldName)
 * @method static Document getDocument(string $fileCabinetId, int $documentId)
 * @method static string getDocumentPreview(string $fileCabinetId, int $documentId)
 * @method static string downloadDocument(string $fileCabinetId, int $documentId)
 * @method static string downloadDocuments(string $fileCabinetId, array $documentIds)
 * @method DocumentThumbnail downloadDocumentThumbnail(string $fileCabinetId, int $documentId, int $section, int $page = 0)
 * @method static null|int|float|Carbon|string updateDocumentValue(string $fileCabinetId, int $documentId, string $fieldName, string $newValue, bool $forceUpdate = false)
 * @method static null|int|float|Carbon|string updateDocumentValues(string $fileCabinetId, int $documentId, array $values, bool $forceUpdate = false)
 * @method static Document uploadDocument(string $fileCabinetId, string $fileContent, string $fileName, ?Collection $indexes = null)
 * @method static int documentCount(string $fileCabinetId, string $dialogId)
 * @method static void deleteDocument(string $fileCabinetId, int $documentId)
 * @method static DocuWareSearchRequestBuilder search()
 * @method static DocuWareUrl url()
 */
class DocuWare extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \CodebarAg\DocuWare\DocuWare::class;
    }
}
