<?php

use CodebarAg\DocuWare\Connectors\DocuWareWithoutCookieConnector;
use CodebarAg\DocuWare\Requests\Document\DeleteDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentsRequest;
use CodebarAg\DocuWare\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

beforeAll(function () {
    $connector = new DocuWareWithoutCookieConnector(config('docuware.cookies'));

    $documents = $connector->send(new GetDocumentsRequest(
        config('docuware.tests.file_cabinet_id')
    ))->dto();

    foreach ($documents as $document) {
        $connector->send(new DeleteDocumentRequest(
            config('docuware.tests.file_cabinet_id'),
            $document->id,
        ))->dto();
    }
});
