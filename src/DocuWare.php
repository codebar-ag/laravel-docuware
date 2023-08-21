<?php

namespace CodebarAg\DocuWare;

use Carbon\Carbon;
use CodebarAg\DocuWare\DTO\Dialog;
use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\DTO\Field;
use CodebarAg\DocuWare\DTO\FileCabinet;
use CodebarAg\DocuWare\DTO\Organization;
use CodebarAg\DocuWare\DTO\OrganizationIndex;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Exceptions\UnableToDownloadDocuments;
use CodebarAg\DocuWare\Exceptions\UnableToGetDocumentCount;
use CodebarAg\DocuWare\Exceptions\UnableToLogin;
use CodebarAg\DocuWare\Exceptions\UnableToLoginNoCookies;
use CodebarAg\DocuWare\Exceptions\UnableToLogout;
use CodebarAg\DocuWare\Requests\Auth\GetLogoffRequest;
use CodebarAg\DocuWare\Requests\Auth\PostLogonRequest;
use CodebarAg\DocuWare\Requests\Document\DeleteDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentCountRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentDownloadRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentPreviewRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentsDownloadRequest;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\PutDocumentFieldRequest;
use CodebarAg\DocuWare\Requests\Document\Thumbnail\GetDocumentDownloadThumbnailRequest;
use CodebarAg\DocuWare\Requests\GetCabinetsRequest;
use CodebarAg\DocuWare\Requests\GetDialogsRequest;
use CodebarAg\DocuWare\Requests\GetFieldsRequest;
use CodebarAg\DocuWare\Requests\GetSelectListRequest;
use CodebarAg\DocuWare\Requests\Organization\GetOrganizationRequest;
use CodebarAg\DocuWare\Requests\Organization\GetOrganizationsRequest;
use CodebarAg\DocuWare\Support\Auth;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use CodebarAg\DocuWare\Support\EnsureValidCredentials;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use CodebarAg\DocuWare\Support\ParseValue;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Saloon\Exceptions\InvalidResponseClassException;
use Saloon\Exceptions\PendingRequestException;
use Symfony\Component\HttpFoundation\Response;

class DocuWare
{
    /**
     * @throws InvalidResponseClassException
     * @throws \Throwable
     * @throws \ReflectionException
     * @throws PendingRequestException
     */
    public function login(): void
    {
        if (config('docuware.cookies')) {
            Auth::store(
                CookieJar::fromArray(
                    [Auth::COOKIE_NAME => config('docuware.cookies')],
                    config('docuware.credentials.url'),
                ),
            );

            return;
        }

        EnsureValidCredentials::check();

        $connection = new DocuWareConnector();

        $request = new PostLogonRequest();

        $response = $connection->send($request);

        event(new DocuWareResponseLog($response));

        throw_if($response->status() === Response::HTTP_UNAUTHORIZED, UnableToLogin::create());
        throw_if($connection->getCoookieJar()->toArray() === [], UnableToLoginNoCookies::create());

        Auth::store($connection->getCoookieJar());
    }

    /**
     * @throws InvalidResponseClassException
     * @throws \Throwable
     * @throws \ReflectionException
     * @throws PendingRequestException
     */
    public function logout(): void
    {
        throw_if(config('docuware.cookies'), UnableToLogout::create());

        EnsureValidCookie::check();

        $connection = new DocuWareConnector();
        $request = new GetLogoffRequest();

        $response = $connection->send($request);

        event(new DocuWareResponseLog($response));

        Auth::forget();

        $response->throw();
    }

    /**
     * @throws InvalidResponseClassException
     * @throws \ReflectionException
     * @throws PendingRequestException
     */
    public function getOrganization(string $organizationId)
    {
        EnsureValidCookie::check();

        $connection = new DocuWareConnector();
        $request = new GetOrganizationRequest($organizationId);

        $response = $connection->send($request);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $organization = $response->throw()->json();

        return Organization::fromJson($organization);
    }

    /**
     * @throws \ReflectionException
     * @throws InvalidResponseClassException
     * @throws PendingRequestException
     */
    public function getOrganizations(): Collection
    {
        EnsureValidCookie::check();

        $connection = new DocuWareConnector();
        $request = new GetOrganizationsRequest();

        $response = $connection->send($request);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $organizations = $response->throw()->json('Organization');

        return collect($organizations)->map(fn (array $cabinet) => OrganizationIndex::fromJson($cabinet));
    }

    /**
     * @throws \ReflectionException
     * @throws InvalidResponseClassException
     * @throws PendingRequestException
     */
    public function getFileCabinets(): Collection
    {
        EnsureValidCookie::check();

        $connection = new DocuWareConnector();
        $request = new GetCabinetsRequest();

        $response = $connection->send($request);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $cabinets = $response->throw()->json('FileCabinet');

        return collect($cabinets)->map(fn (array $cabinet) => FileCabinet::fromJson($cabinet));
    }

    /**
     * @throws \ReflectionException
     * @throws InvalidResponseClassException
     * @throws PendingRequestException
     */
    public function getFields(string $fileCabinetId): Collection
    {
        EnsureValidCookie::check();

        $connection = new DocuWareConnector();
        $request = new GetFieldsRequest(fileCabinetId: $fileCabinetId);

        $response = $connection->send($request);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $fields = $response->throw()->json('Fields');

        return collect($fields)->map(fn (array $field) => Field::fromJson($field));
    }

    /**
     * @throws \ReflectionException
     * @throws InvalidResponseClassException
     * @throws PendingRequestException
     */
    public function getDialogs(string $fileCabinetId): Collection
    {
        EnsureValidCookie::check();

        $connection = new DocuWareConnector();
        $request = new GetDialogsRequest(fileCabinetId: $fileCabinetId);

        $response = $connection->send($request);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $dialogs = $response->throw()->json('Dialog');

        return collect($dialogs)->map(fn (array $dialog) => Dialog::fromJson($dialog));
    }

    /**
     * @throws \ReflectionException
     * @throws InvalidResponseClassException
     * @throws PendingRequestException
     */
    public function getSelectList(
        string $fileCabinetId,
        string $dialogId,
        string $fieldName,
    ): array {
        EnsureValidCookie::check();

        $connection = new DocuWareConnector();
        $request = new GetSelectListRequest(
            fileCabinetId: $fileCabinetId,
            dialogId: $dialogId,
            fieldName: $fieldName
        );

        $response = $connection->send($request);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->json('Value');
    }

    /**
     * @throws \ReflectionException
     * @throws InvalidResponseClassException
     * @throws PendingRequestException
     */
    public function getDocument(string $fileCabinetId, int $documentId): Document
    {
        EnsureValidCookie::check();

        $connection = new DocuWareConnector();
        $request = new GetDocumentRequest(
            fileCabinetId: $fileCabinetId,
            documentId: $documentId,
        );

        $response = $connection->send($request);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $data = $response->throw()->json();

        return Document::fromJson($data);
    }

    /**
     * @throws \ReflectionException
     * @throws InvalidResponseClassException
     * @throws PendingRequestException
     */
    public function getDocumentPreview(string $fileCabinetId, int $documentId): string
    {
        EnsureValidCookie::check();

        $connection = new DocuWareConnector();
        $request = new GetDocumentPreviewRequest(
            fileCabinetId: $fileCabinetId,
            documentId: $documentId,
        );

        $response = $connection->send($request);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->body();
    }

    /**
     * @throws \ReflectionException
     * @throws InvalidResponseClassException
     * @throws PendingRequestException
     */
    public function downloadDocument(string $fileCabinetId, int $documentId): string
    {
        EnsureValidCookie::check();

        $connection = new DocuWareConnector();
        $request = new GetDocumentDownloadRequest(
            fileCabinetId: $fileCabinetId,
            documentId: $documentId,
        );

        $response = $connection->send($request);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->body();
    }

    /**
     * @throws InvalidResponseClassException
     * @throws \Throwable
     * @throws \ReflectionException
     * @throws PendingRequestException
     */
    public function downloadDocuments(string $fileCabinetId, array $documentIds): string
    {
        EnsureValidCookie::check();

        throw_if(
            count($documentIds) < 2,
            UnableToDownloadDocuments::selectAtLeastTwoDocuments(),
        );

        $firstDocumentId = $documentIds[0];
        $additionalDocumentIds = array_slice($documentIds, 1);

        $connection = new DocuWareConnector();
        $request = new GetDocumentsDownloadRequest(
            fileCabinetId: $fileCabinetId,
            documentId: $firstDocumentId,
            additionalDocumentIds: $additionalDocumentIds,
        );

        $response = $connection->send($request);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->body();
    }

    /**
     * @throws InvalidResponseClassException
     * @throws \ReflectionException
     * @throws PendingRequestException
     */
    public function updateDocumentValue(
        string $fileCabinetId,
        int $documentId,
        string $fieldName,
        string $newValue,
        bool $forceUpdate = false,
    ): null|int|float|Carbon|string {
        $fields = $this->updateDocumentValues(
            fileCabinetId: $fileCabinetId,
            documentId: $documentId,
            values: [$fieldName => $newValue],
            forceUpdate: $forceUpdate,
        );

        return collect($fields)->get($fieldName) ?? null;
    }

    /**
     * @throws \ReflectionException
     * @throws InvalidResponseClassException
     * @throws PendingRequestException
     */
    public function updateDocumentValues(
        string $fileCabinetId,
        int $documentId,
        array $values,
        bool $forceUpdate = false,
    ): ?Collection {
        EnsureValidCookie::check();

        $connection = new DocuWareConnector();
        $request = new PutDocumentFieldRequest(
            fileCabinetId: $fileCabinetId,
            documentId: $documentId,
            values: $values,
            forceUpdate: $forceUpdate,
        );

        $response = $connection->send($request);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $fields = $response->throw()->json('Field');

        return collect($fields)->mapWithKeys(function (array $field) {
            return [
                $field['FieldName'] => ParseValue::field($field),
            ];
        });
    }

    /**
     * @throws InvalidResponseClassException
     * @throws RequestException
     * @throws \ReflectionException
     * @throws PendingRequestException
     */
    public function uploadDocument(
        string $fileCabinetId,
        string $fileContent,
        string $fileName,
        Collection $indexes = null,
    ): Document {
        EnsureValidCookie::check();

        $connection = new DocuWareConnector();
        $request = new PostDocumentRequest(
            fileCabinetId: $fileCabinetId,
            fileContent: $fileContent,
            fileName: $fileName,
            indexes: $indexes,
        );

        $response = $connection->send($request);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $data = $response->throw()->json();

        return Document::fromJson($data);
    }

    /**
     * @throws InvalidResponseClassException
     * @throws RequestException
     * @throws \ReflectionException
     * @throws PendingRequestException
     */
    public function deleteDocument(
        string $fileCabinetId,
        int $documentId,
    ): void {
        EnsureValidCookie::check();

        $connection = new DocuWareConnector();
        $request = new DeleteDocumentRequest(
            fileCabinetId: $fileCabinetId,
            documentId: $documentId,
        );

        $response = $connection->send($request);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $response->throw();
    }

    public function downloadDocumentThumbnail(string $fileCabinetId, int $documentId, int $section, int $page = 0): string
    {
        EnsureValidCookie::check();

        $connection = new DocuWareConnector();
        $request = new GetDocumentDownloadThumbnailRequest(
            fileCabinetId: $fileCabinetId,
            documentId: $documentId,
            section: $section,
            page: $page,
        );

        $response = $connection->send($request);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->body();
    }

    public function documentCount(string $fileCabinetId, string $dialogId): int
    {
        EnsureValidCookie::check();
        $connection = new DocuWareConnector();
        $request = new GetDocumentCountRequest(
            fileCabinetId: $fileCabinetId,
            dialogId: $dialogId,
        );

        $response = $connection->send($request);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $content = $response->throw()->json();
        throw_unless(Arr::has($content, 'Group'), UnableToGetDocumentCount::noCount());

        $group = Arr::get($content, 'Group');
        throw_unless(Arr::has($group, '0'), UnableToGetDocumentCount::noGroupKeyIndexZero());
        $group = Arr::get($group, '0');

        throw_unless(Arr::has($group, 'Count'), UnableToGetDocumentCount::noCount());

        return Arr::get($group, 'Count');
    }

    public function search(): DocuWareSearch
    {
        return new DocuWareSearch();
    }

    public function url(): DocuWareUrl
    {
        return new DocuWareUrl();
    }
}
