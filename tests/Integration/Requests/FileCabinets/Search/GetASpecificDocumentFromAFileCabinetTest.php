<?php

use CodebarAg\DocuWare\DTO\Documents\Document;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetASpecificDocumentFromAFileCabinet;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('can show a document', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    Sleep::for(2)->seconds(); // Wait for the document to be processed

    $getdocument = $this->connector->send(new GetASpecificDocumentFromAFileCabinet($fileCabinetId, $document->id))->dto();

    $this->assertInstanceOf(Document::class, $getdocument);

    $this->assertSame($document->id, $getdocument->id);
    $this->assertSame($fileCabinetId, $getdocument->file_cabinet_id);
    Event::assertDispatched(DocuWareResponseLog::class);

});
