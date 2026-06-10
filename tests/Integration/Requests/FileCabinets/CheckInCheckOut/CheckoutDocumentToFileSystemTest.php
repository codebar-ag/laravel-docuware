<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\CheckInCheckOut\CheckoutDocumentToFileSystem;
use CodebarAg\DocuWare\Requests\FileCabinets\CheckInCheckOut\UndoDocumentCheckout;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('checks out a document to the file system', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    Sleep::for(2)->seconds();

    $checkout = $this->connector->send(new CheckoutDocumentToFileSystem(
        $fileCabinetId,
        $document->id,
    ))->dto();

    expect($checkout->links)->not->toBeEmpty();

    Event::assertDispatched(DocuWareResponseLog::class);

    Event::fake();

    $this->connector->send(new UndoDocumentCheckout(
        $fileCabinetId,
        $document->id,
    ))->dto();
})->skip(
    fn () => ! filter_var(config('laravel-docuware.tests.version_management_enabled'), FILTER_VALIDATE_BOOLEAN),
    'Set DOCUWARE_TESTS_VERSION_MANAGEMENT_ENABLED=true when the test file cabinet has version management enabled.',
);
