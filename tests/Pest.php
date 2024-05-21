<?php

use CodebarAg\DocuWare\Connectors\DocuWareConnector;
use CodebarAg\DocuWare\DTO\Config\ConfigWithCredentials;
use CodebarAg\DocuWare\Requests\Documents\ModifyDocuments\DeleteDocument;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetDocumentsFromAFileCabinet;
use CodebarAg\DocuWare\Requests\General\UserManagement\CreateUpdateUsers\UpdateUser;
use CodebarAg\DocuWare\Requests\General\UserManagement\GetUsers\GetUsers;
use CodebarAg\DocuWare\Tests\TestCase;

uses(TestCase::class)
    ->in(__DIR__);

uses()
    ->beforeEach(function () {
        $this->connector = getConnector();

        clearFiles();
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
}

function setUsersInactive(): void
{
    $connector = getConnector();

    $response = $connector->send(new GetUsers());

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
