<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetDocumentsFromAFileCabinet;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;

it('can get all documents', function () {
    Event::fake();

    $this->connector->send(new CreateDataRecord(
        env('DOCUWARE_TESTS_FILE_CABINET_ID'),
        '::fake-file-content::',
        'example.txt'
    ))->dto();
    $this->connector->send(new CreateDataRecord(
        env('DOCUWARE_TESTS_FILE_CABINET_ID'),
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $documents = $this->connector->send(new GetDocumentsFromAFileCabinet(
        env('DOCUWARE_TESTS_FILE_CABINET_ID')
    ))->dto();

    ray($documents);

    Event::assertDispatched(DocuWareResponseLog::class);
});
