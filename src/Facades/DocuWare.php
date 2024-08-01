<?php

namespace CodebarAg\DocuWare\Facades;

use Carbon\Carbon;
use CodebarAg\DocuWare\DocuWareSearchRequestBuilder;
use CodebarAg\DocuWare\DocuWareUrl;
use CodebarAg\DocuWare\DTO\Config\ConfigWithCredentials;
use CodebarAg\DocuWare\DTO\Config\ConfigWithCredentialsTrustedUser;
use CodebarAg\DocuWare\DTO\Cookie;
use CodebarAg\DocuWare\DTO\Documents\Document;
use CodebarAg\DocuWare\DTO\Documents\DocumentThumbnail;
use CodebarAg\DocuWare\DTO\Documents\Field;
use CodebarAg\DocuWare\DTO\FileCabinets\Dialog;
use CodebarAg\DocuWare\DTO\FileCabinets\General\FileCabinetInformation;
use CodebarAg\DocuWare\DTO\General\Organization\Organization;
use CodebarAg\DocuWare\DTO\General\Organization\OrganizationIndex;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @see \CodebarAg\DocuWare\DocuWare
 *
 * @method static Cookie cookie(string $url, string $username, string $password, $rememberMe = false, $redirectToMyselfInCaseOfError = false, $licenseType = null)
 * @method static string login()
 * @method static void logout()
 * @method static Organization getOrganization(string $organizationId)
 * @method static Collection|OrganizationIndex[] getOrganizations()
 * @method static Collection|FileCabinetInformation[] getFileCabinets()
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
 * @method static DocuWareUrl url(null|ConfigWithCredentials|ConfigWithCredentialsTrustedUser $configuration = null)
 */
class DocuWare extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \CodebarAg\DocuWare\DocuWare::class;
    }
}
