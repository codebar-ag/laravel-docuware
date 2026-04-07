<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetDocumentsFromAFileCabinet;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

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

    Sleep::for(2)->seconds(); // Wait for the documents to be processed

    $documents = $this->connector->send(new GetDocumentsFromAFileCabinet(
        config('laravel-docuware.tests.file_cabinet_id')
    ))->dto();

    Event::assertDispatched(DocuWareResponseLog::class);
});
