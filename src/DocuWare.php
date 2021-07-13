<?php

namespace CodebarAg\DocuWare;

use Carbon\Carbon;
use CodebarAg\DocuWare\DTO\Dialog;
use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\DTO\DocumentIndex;
use CodebarAg\DocuWare\DTO\Field;
use CodebarAg\DocuWare\DTO\FileCabinet;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Exceptions\UnableToDownloadDocuments;
use CodebarAg\DocuWare\Exceptions\UnableToLogin;
use CodebarAg\DocuWare\Support\Auth;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use CodebarAg\DocuWare\Support\EnsureValidCredentials;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use CodebarAg\DocuWare\Support\ParseValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class DocuWare
{
    public function login(): void
    {
        EnsureValidCredentials::check();

        $url = sprintf(
            '%s/DocuWare/Platform/Account/Logon',
            config('docuware.credentials.url'),
        );

        $response = Http::asForm()
            ->acceptJson()
            ->post($url, [
                'UserName' => config('docuware.credentials.username'),
                'Password' => config('docuware.credentials.password'),
            ]);

        event(new DocuWareResponseLog($response));

        throw_if(
            $response->status() === Response::HTTP_UNAUTHORIZED,
            UnableToLogin::create(),
        );

        $cookies = $response->throw()->cookies();

        Auth::store($cookies);
    }

    public function logout(): void
    {
        EnsureValidCookie::check();

        $url = sprintf(
            '%s/DocuWare/Platform/Account/Logoff',
            config('docuware.credentials.url'),
        );

        $response = Http::withCookies(Auth::cookies(), Auth::domain())->get($url);

        event(new DocuWareResponseLog($response));

        Auth::forget();

        $response->throw();
    }

    public function getFileCabinets(): Collection
    {
        EnsureValidCookie::check();

        $url = sprintf(
            '%s/DocuWare/Platform/FileCabinets',
            config('docuware.credentials.url'),
        );

        $response = Http::acceptJson()
            ->withCookies(Auth::cookies(), Auth::domain())
            ->get($url);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $cabinets = $response->throw()->json('FileCabinet');

        return collect($cabinets)->map(fn (array $cabinet) => FileCabinet::fromJson($cabinet));
    }

    public function getFields(string $fileCabinetId): Collection
    {
        EnsureValidCookie::check();

        $url = sprintf(
            '%s/DocuWare/Platform/FileCabinets/%s',
            config('docuware.credentials.url'),
            $fileCabinetId,
        );

        $response = Http::acceptJson()
            ->withCookies(Auth::cookies(), Auth::domain())
            ->get($url);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $fields = $response->throw()->json('Fields');

        return collect($fields)->map(fn (array $field) => Field::fromJson($field));
    }

    public function getDialogs(string $fileCabinetId): Collection
    {
        EnsureValidCookie::check();

        $url = sprintf(
            '%s/DocuWare/Platform/FileCabinets/%s/Dialogs',
            config('docuware.credentials.url'),
            $fileCabinetId,
        );

        $response = Http::acceptJson()
            ->withCookies(Auth::cookies(), Auth::domain())
            ->get($url);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $dialogs = $response->throw()->json('Dialog');

        return collect($dialogs)->map(fn (array $dialog) => Dialog::fromJson($dialog));
    }

    public function getSelectList(
        string $fileCabinetId,
        string $dialogId,
        string $fieldName,
    ): array {
        EnsureValidCookie::check();

        $url = sprintf(
            '%s/DocuWare/Platform/FileCabinets/%s/Query/SelectListExpression?dialogId=%s&fieldName=%s',
            config('docuware.credentials.url'),
            $fileCabinetId,
            $dialogId,
            $fieldName,
        );

        $response = Http::acceptJson()
            ->withCookies(Auth::cookies(), Auth::domain())
            ->get($url);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->json('Value');
    }

    public function getDocument(string $fileCabinetId, int $documentId): Document
    {
        EnsureValidCookie::check();

        $url = sprintf(
            '%s/DocuWare/Platform/FileCabinets/%s/Documents/%s',
            config('docuware.credentials.url'),
            $fileCabinetId,
            $documentId,
        );

        $response = Http::acceptJson()
            ->withCookies(Auth::cookies(), Auth::domain())
            ->get($url);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $data = $response->throw()->json();

        return Document::fromJson($data);
    }

    public function getDocumentPreview(
        string $fileCabinetId,
        int $documentId,
    ): string {
        EnsureValidCookie::check();

        $url = sprintf(
            '%s/DocuWare/Platform/FileCabinets/%s/Documents/%s/Image',
            config('docuware.credentials.url'),
            $fileCabinetId,
            $documentId,
        );

        $response = Http::acceptJson()
            ->withCookies(Auth::cookies(), Auth::domain())
            ->get($url);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->body();
    }

    public function downloadDocument(
        string $fileCabinetId,
        int $documentId,
    ): string {
        EnsureValidCookie::check();

        $url = sprintf(
            '%s/DocuWare/Platform/FileCabinets/%s/Documents/%s/FileDownload?targetFileType=Auto&keepAnnotations=false',
            config('docuware.credentials.url'),
            $fileCabinetId,
            $documentId,
        );

        $response = Http::acceptJson()
            ->withCookies(Auth::cookies(), Auth::domain())
            ->get($url);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->body();
    }

    public function downloadDocuments(
        string $fileCabinetId,
        array $documentIds,
    ): string {
        EnsureValidCookie::check();

        throw_if(
            count($documentIds) < 2,
            UnableToDownloadDocuments::selectAtLeastTwoDocuments(),
        );

        $firstDocumentId = $documentIds[0];
        $additionalDocumentIds = implode(',', array_slice($documentIds, 1));

        $url = sprintf(
            '%s/DocuWare/Platform/FileCabinets/%s/Documents/%s/FileDownload?&keepAnnotations=false&append=%s',
            config('docuware.credentials.url'),
            $fileCabinetId,
            $firstDocumentId,
            $additionalDocumentIds,
        );

        $response = Http::acceptJson()
            ->withCookies(Auth::cookies(), Auth::domain())
            ->get($url);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->body();
    }

    public function updateDocumentValue(
        string $fileCabinetId,
        int $documentId,
        string $fieldName,
        string $newValue,
    ): null | int | float | Carbon | string {
        EnsureValidCookie::check();

        $url = sprintf(
            '%s/DocuWare/Platform/FileCabinets/%s/Documents/%s/Fields',
            config('docuware.credentials.url'),
            $fileCabinetId,
            $documentId,
        );

        $response = Http::acceptJson()
            ->withCookies(Auth::cookies(), Auth::domain())
            ->put($url, [
                'Field' => [
                    [
                        'FieldName' => $fieldName,
                        'Item' => $newValue,
                    ],
                ],
            ]);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $fields = $response->throw()->json('Field');

        $field = collect($fields)->firstWhere('FieldName', $fieldName);

        return ParseValue::field($field);
    }

    public function uploadDocument(
        string $fileCabinetId,
        string $fileContent,
        string $fileName,
        ?Collection $indexes = null,
    ): Document {
        EnsureValidCookie::check();

        $url = sprintf(
            '%s/DocuWare/Platform/FileCabinets/%s/Documents',
            config('docuware.credentials.url'),
            $fileCabinetId,
        );

        $request = Http::acceptJson();

        if ($indexes) {
            $indexContent = DocumentIndex::makeContent($indexes);

            $request->attach('document', $indexContent, 'index.json');
        }

        $response = $request->attach('file', $fileContent, $fileName)
            ->withCookies(Auth::cookies(), Auth::domain())
            ->post($url);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $data = $response->throw()->json();

        return Document::fromJson($data);
    }

    public function deleteDocument(
        string $fileCabinetId,
        int $documentId,
    ): void {
        EnsureValidCookie::check();

        $url = sprintf(
            '%s/DocuWare/Platform/FileCabinets/%s/Documents/%s',
            config('docuware.credentials.url'),
            $fileCabinetId,
            $documentId,
        );

        $response = Http::acceptJson()
            ->withCookies(Auth::cookies(), Auth::domain())
            ->delete($url);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $response->throw();
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
