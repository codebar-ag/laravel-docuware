<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\CheckInCheckOut\CheckInDocumentFromFileSystem;
use CodebarAg\DocuWare\Requests\FileCabinets\CheckInCheckOut\CheckoutDocumentToFileSystem;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('checks in a checked-out document from the file system', function () {
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

    $checkInJson = json_encode([
        'DocumentVersion' => 1,
        'Comments' => 'integration check-in',
    ], JSON_THROW_ON_ERROR);

    $checkedIn = $this->connector->send(new CheckInDocumentFromFileSystem(
        $fileCabinetId,
        $document->id,
        $checkInJson,
        '::fake-file-content::',
        'example.txt',
    ))->dto();

    expect($checkedIn->id)->toBe($document->id);

    Event::assertDispatched(DocuWareResponseLog::class);
})->skip('Check-in requires a prior check-out and version management on the file cabinet.');
