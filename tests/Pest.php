<?php

use CodebarAg\DocuWare\Connectors\DocuWareConnector;
use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\DTO\Config\ConfigWithCredentials;
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
    ->beforeEach(function () {
        $this->connector = getConnector();

        //clearFiles();
    })
    ->afterEach(function () {
        setUsersInactive();
    })
    ->in('Feature');

function clearFiles(): void
{
    $connector = getConnector();

    $paginator = $connector->send(new GetDocumentsFromAFileCabinet(
        config('laravel-docuware.tests.file_cabinet_id')
    ))->dto();

    foreach ($paginator->documents as $document) {
        $connector->send(new DeleteDocument(
            config('laravel-docuware.tests.file_cabinet_id'),
            $document->id,
        ))->dto();
    }

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

function setUsersInactive(): void
{
    $connector = getConnector();

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
function getConnector(): object
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

function uploadFiles($connector, $fileCabinetId, $path): array
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

    Sleep::for(5)->seconds(); // Wait for the files to be uploaded and processed

    // Have to get document again as returned data is incorrect
    $document = $connector->send(new GetASpecificDocumentFromAFileCabinet(
        $fileCabinetId,
        $document->id
    ))->dto();

    // Have to get document2 again as returned data is incorrect
    $document2 = $connector->send(new GetASpecificDocumentFromAFileCabinet(
        $fileCabinetId,
        $document2->id
    ))->dto();

    return [$document, $document2];
}
