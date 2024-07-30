<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\Download\DownloadDocument;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;

it('can download a document', function () {
    Event::fake();

    $fileCabinetId = env('DOCUWARE_TESTS_FILE_CABINET_ID');

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $contents = $this->connector->send(new DownloadDocument(
        $fileCabinetId,
        $document->id
    ))->dto();

    $this->assertSame(strlen('::fake-file-content::'), strlen($contents));
    Event::assertDispatched(DocuWareResponseLog::class);

})->group('download');
