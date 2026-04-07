<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\Stamps\GetDocumentAnnotations;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('returns annotations as a collection for a document', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    Sleep::for(2)->seconds();

    $response = $this->connector->send(new GetDocumentAnnotations(
        $fileCabinetId,
        $document->id
    ));

    expect($response->successful())->toBeTrue();

    $dto = $response->dto();
    expect($dto)->toBeInstanceOf(Collection::class);

    Event::assertDispatched(DocuWareResponseLog::class);
});
