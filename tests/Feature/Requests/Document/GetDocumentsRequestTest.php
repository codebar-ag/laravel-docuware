<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Document\GetDocumentsRequest;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use Illuminate\Support\Facades\Event;

it('can get all documents', function () {
    Event::fake();

    $this->connector->send(new PostDocumentRequest(
        config('laravel-docuware.tests.file_cabinet_id'),
        '::fake-file-content::',
        'example.txt'
    ))->dto();
    $this->connector->send(new PostDocumentRequest(
        config('laravel-docuware.tests.file_cabinet_id'),
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $documents = $this->connector->send(new GetDocumentsRequest(
        config('laravel-docuware.tests.file_cabinet_id')
    ))->dto();

    Event::assertDispatched(DocuWareResponseLog::class);
});
