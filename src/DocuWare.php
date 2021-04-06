<?php

namespace CodebarAg\DocuWare;

use Carbon\Carbon;
use CodebarAg\DocuWare\DTO\Dialog;
use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\DTO\Field;
use CodebarAg\DocuWare\DTO\FileCabinet;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Exceptions\UnableToDownloadDocuments;
use CodebarAg\DocuWare\Exceptions\UnableToLogin;
use CodebarAg\DocuWare\Support\EnsureValidCredentials;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use CodebarAg\DocuWare\Support\ParseValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class DocuWare
{
    const COOKIE_NAME = '.DWPLATFORMAUTH';

    protected string $domain;

    public function __construct()
    {
        EnsureValidCredentials::check();

        $this->domain = ParseValue::domain();
    }

    public function login(): string
    {
        if (Cache::has('docuware.cookies')) {
            return Cache::get('docuware.cookies')[self::COOKIE_NAME];
        }

        $url = sprintf(
            '%s/docuware/platform/Account/Logon',
            config('docuware.url'),
        );

        $response = Http::asForm()
            ->acceptJson()
            ->post($url, [
                'UserName' => config('docuware.user'),
                'Password' => config('docuware.password'),
            ]);

        event(new DocuWareResponseLog($response));

        throw_if(
            $response->status() === Response::HTTP_UNAUTHORIZED,
            UnableToLogin::create(),
        );

        $cookies = $response
            ->throw()
            ->cookies()
            ->toArray();

        $cookie = collect($cookies)
            ->reject(fn (array $cookie) => $cookie['Value'] === '')
            ->firstWhere('Name', self::COOKIE_NAME);

        Cache::put(
            'docuware.cookies',
            [self::COOKIE_NAME => $cookie['Value']],
            now()->addDay(),
        );

        return $cookie['Value'];
    }

    public function logout(): void
    {
        $cookie = Cache::pull('docuware.cookies');

        $url = sprintf(
            '%s/docuware/platform/Account/Logoff',
            config('docuware.url'),
        );

        $response = Http::withCookies($cookie, $this->domain)
            ->get($url)
            ->throw();

        event(new DocuWareResponseLog($response));
    }

    public function getFileCabinets(): Collection
    {
        $url = sprintf(
            '%s/docuware/platform/FileCabinets',
            config('docuware.url'),
        );

        $response = Http::acceptJson()
            ->withCookies(Cache::get('docuware.cookies'), $this->domain)
            ->get($url);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $cabinets = $response->throw()->json('FileCabinet');

        return collect($cabinets)->map(fn (array $cabinet) => FileCabinet::fromJson($cabinet));
    }

    public function getFields(string $fileCabinetId): Collection
    {
        $url = sprintf(
            '%s/docuware/platform/FileCabinets/%s',
            config('docuware.url'),
            $fileCabinetId,
        );

        $response = Http::acceptJson()
            ->withCookies(Cache::get('docuware.cookies'), $this->domain)
            ->get($url);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $fields = $response->throw()->json('Fields');

        return collect($fields)->map(fn (array $field) => Field::fromJson($field));
    }

    public function getDialogs(string $fileCabinetId): Collection
    {
        $url = sprintf(
            '%s/docuware/platform/FileCabinets/%s/Dialogs',
            config('docuware.url'),
            $fileCabinetId,
        );

        $response = Http::acceptJson()
            ->withCookies(Cache::get('docuware.cookies'), $this->domain)
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
        $url = sprintf(
            '%s/docuware/platform/FileCabinets/%s/Query/SelectListExpression?dialogId=%s&fieldName=%s',
            config('docuware.url'),
            $fileCabinetId,
            $dialogId,
            $fieldName,
        );

        $response = Http::acceptJson()
            ->withCookies(Cache::get('docuware.cookies'), $this->domain)
            ->get($url);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->json('Value');
    }

    public function getDocument(string $fileCabinetId, int $documentId): Document
    {
        $url = sprintf(
            '%s/docuware/platform/FileCabinets/%s/Documents/%s',
            config('docuware.url'),
            $fileCabinetId,
            $documentId,
        );

        $response = Http::acceptJson()
            ->withCookies(Cache::get('docuware.cookies'), $this->domain)
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
        $url = sprintf(
            '%s/docuware/platform/FileCabinets/%s/Documents/%s/Image',
            config('docuware.url'),
            $fileCabinetId,
            $documentId,
        );

        $response = Http::acceptJson()
            ->withCookies(Cache::get('docuware.cookies'), $this->domain)
            ->get($url);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->body();
    }

    public function downloadDocument(
        string $fileCabinetId,
        int $documentId,
    ): string {
        $url = sprintf(
            '%s/docuware/platform/FileCabinets/%s/Documents/%s/FileDownload?targetFileType=Auto&keepAnnotations=false',
            config('docuware.url'),
            $fileCabinetId,
            $documentId
        );

        $response = Http::acceptJson()
            ->withCookies(Cache::get('docuware.cookies'), $this->domain)
            ->get($url);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        return $response->throw()->body();
    }

    public function downloadDocuments(
        string $fileCabinetId,
        array $documentIds,
    ): string {
        throw_if(
            count($documentIds) < 2,
            UnableToDownloadDocuments::selectAtLeastTwoDocuments(),
        );

        $firstDocumentId = $documentIds[0];
        $additionalDocumentIds = implode(',', array_slice($documentIds, 1));

        $url = sprintf(
            '%s/docuware/platform/FileCabinets/%s/Documents/%s/FileDownload?&keepAnnotations=false&append=%s',
            config('docuware.url'),
            $fileCabinetId,
            $firstDocumentId,
            $additionalDocumentIds,
        );

        $response = Http::acceptJson()
            ->withCookies(Cache::get('docuware.cookies'), $this->domain)
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
        $url = sprintf(
            '%s/docuware/platform/FileCabinets/%s/Documents/%s/Fields',
            config('docuware.url'),
            $fileCabinetId,
            $documentId,
        );

        $response = Http::acceptJson()
            ->withCookies(Cache::get('docuware.cookies'), $this->domain)
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
    ): Document {
        $url = sprintf(
            '%s/docuware/platform/FileCabinets/%s/Documents',
            config('docuware.url'),
            $fileCabinetId,
        );

        $response = Http::acceptJson()
            ->withCookies(Cache::get('docuware.cookies'), $this->domain)
            ->attach('file', $fileContent, $fileName)
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
        $url = sprintf(
            '%s/docuware/platform/FileCabinets/%s/Documents/%s',
            config('docuware.url'),
            $fileCabinetId,
            $documentId,
        );

        $response = Http::acceptJson()
            ->withCookies(Cache::get('docuware.cookies'), $this->domain)
            ->delete($url);

        event(new DocuWareResponseLog($response));

        EnsureValidResponse::from($response);

        $response->throw();
    }

    public function search(): DocuWareSearch
    {
        return (new DocuWareSearch());
    }
}
