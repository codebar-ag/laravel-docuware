<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\Stamps\AddDocumentAnnotations;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('posts annotations payload to the document annotation endpoint', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    Sleep::for(2)->seconds();

    $response = $this->connector->send(new AddDocumentAnnotations(
        $fileCabinetId,
        $document->id,
        integrationTestAnnotationPayload(),
    ));

    expect($response->successful())->toBeTrue();

    Event::assertDispatched(DocuWareResponseLog::class);
});
