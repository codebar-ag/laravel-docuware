<?php

use CodebarAg\DocuWare\Connectors\DocuWareConnector;
use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\DTO\Config\ConfigWithCredentials;
use CodebarAg\DocuWare\DTO\Documents\Document;
use CodebarAg\DocuWare\Requests\Documents\DocumentsTrashBin\DeleteDocuments;
use CodebarAg\DocuWare\Requests\Documents\ModifyDocuments\DeleteDocument;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetASpecificDocumentFromAFileCabinet;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetDocumentsFromAFileCabinet;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers\UpdateUser;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUsers;
use CodebarAg\DocuWare\Tests\TestCase;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;

uses(TestCase::class)
    ->in(__DIR__);

uses()
    ->group('live')
    ->beforeEach(function () {
        $this->connector = getConnector();

        clearFiles($this->connector);
    })
    ->afterEach(function () {
        setUsersInactive($this->connector);
    })
    ->in('Integration');

function clearFiles(DocuWareConnector $connector): void
{
    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $paginator = $connector->send(new GetDocumentsFromAFileCabinet($fileCabinetId))->dto();

    if ($paginator->documents->isEmpty()) {
        emptyTrashForConnector($connector);

        return;
    }

    foreach ($paginator->documents as $document) {
        $connector->send(new DeleteDocument(
            $fileCabinetId,
            $document->id,
        ))->dto();
    }

    emptyTrashForConnector($connector);
}

function emptyTrashForConnector(DocuWareConnector $connector): void
{
    $paginatorRequest = (new DocuWare)
        ->searchRequestBuilder()
        ->trashBin()
        ->perPage(1000)
        ->get();

    $paginator = $connector->send($paginatorRequest)->dto();

    if ($paginator->total > 0) {
        $connector->send(new DeleteDocuments($paginator->mappedDocuments->pluck('ID')->all()))->dto();
    }
}

function setUsersInactive(DocuWareConnector $connector): void
{
    $response = $connector->send(new GetUsers);

    $users = $response->dto()->filter(function ($user) {
        return Str::contains($user->email, 'test@example.test') && $user->active === true;
    });

    foreach ($users as $user) {
        $user->active = false;

        $connector->send(new UpdateUser($user));
    }
}

/**
 * @throws Throwable
 */
function getConnector(): DocuWareConnector
{
    return new DocuWareConnector(new ConfigWithCredentials(
        username: config('laravel-docuware.credentials.username'),
        password: config('laravel-docuware.credentials.password'),
    ));
}

function cleanup($connector, $fileCabinetId): void
{
    $paginator = $connector->send(new GetDocumentsFromAFileCabinet(
        $fileCabinetId
    ))->dto();

    foreach ($paginator->documents as $document) {
        $connector->send(new DeleteDocument(
            $fileCabinetId,
            $document->id,
        ))->dto();
    }
}

function documentLooksProcessed(Document $document): bool
{
    return $document->total_pages > 0
        && $document->sections !== null
        && $document->sections->isNotEmpty();
}

function refreshDocumentAfterProcessing(DocuWareConnector $connector, string $fileCabinetId, int $documentId): Document
{
    $maxAttempts = 60;
    $sleepMs = 250;

    for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
        $document = $connector->send(new GetASpecificDocumentFromAFileCabinet(
            $fileCabinetId,
            $documentId
        ))->dto();

        if (documentLooksProcessed($document)) {
            return $document;
        }

        Sleep::for($sleepMs)->milliseconds();
    }

    return $connector->send(new GetASpecificDocumentFromAFileCabinet(
        $fileCabinetId,
        $documentId
    ))->dto();
}

function uploadFiles(DocuWareConnector $connector, $fileCabinetId, $path): array
{
    $document = $connector->send(new CreateDataRecord(
        $fileCabinetId,
        file_get_contents($path.'/test-1.pdf'),
        'test-1.pdf',
    ))->dto();

    $document2 = $connector->send(new CreateDataRecord(
        $fileCabinetId,
        file_get_contents($path.'/test-2.pdf'),
        'test-2.pdf',
    ))->dto();

    $document = refreshDocumentAfterProcessing($connector, $fileCabinetId, $document->id);
    $document2 = refreshDocumentAfterProcessing($connector, $fileCabinetId, $document2->id);

    return [$document, $document2];
}
