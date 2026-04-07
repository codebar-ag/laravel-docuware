<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\CheckInCheckOut\CheckoutDocumentToFileSystem;
use CodebarAg\DocuWare\Requests\FileCabinets\CheckInCheckOut\UndoDocumentCheckout;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('undoes checkout for a document', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    Sleep::for(2)->seconds();

    $this->connector->send(new CheckoutDocumentToFileSystem(
        $fileCabinetId,
        $document->id,
    ))->dto();

    Event::fake();

    $restored = $this->connector->send(new UndoDocumentCheckout(
        $fileCabinetId,
        $document->id,
    ))->dto();

    expect($restored->id)->toBe($document->id);

    Event::assertDispatched(DocuWareResponseLog::class);
})->skip('Check-out requires version management enabled on the file cabinet (often off on cloud test cabinets).');
