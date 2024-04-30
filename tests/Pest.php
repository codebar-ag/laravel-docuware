<?php

use CodebarAg\DocuWare\Connectors\DocuWareConnector;
use CodebarAg\DocuWare\Requests\Documents\ModifyDocuments\DeleteDocument;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetDocumentsFromAFileCabinet;
use CodebarAg\DocuWare\Tests\TestCase;

uses(TestCase::class)
    ->in(__DIR__);

uses()
    ->beforeEach(function () {
        $this->connector = getConnector();

        clearFiles();
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

/**
 * @throws Throwable
 */
function getConnector(): object
{
    return new DocuWareConnector();
}
