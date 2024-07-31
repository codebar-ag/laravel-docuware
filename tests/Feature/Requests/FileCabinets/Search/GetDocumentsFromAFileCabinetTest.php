<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetDocumentsFromAFileCabinet;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;

it('can get all documents', function () {
    Event::fake();

    $this->connector->send(new CreateDataRecord(
        config('laravel-docuware.tests.file_cabinet_id'),
        '::fake-file-content::',
        'example.txt'
    ))->dto();
    $this->connector->send(new CreateDataRecord(
        config('laravel-docuware.tests.file_cabinet_id'),
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $documents = $this->connector->send(new GetDocumentsFromAFileCabinet(
        config('laravel-docuware.tests.file_cabinet_id')
    ))->dto();

    ray($documents);

    Event::assertDispatched(DocuWareResponseLog::class);
});
