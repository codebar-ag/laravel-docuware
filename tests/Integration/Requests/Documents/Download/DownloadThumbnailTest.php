<?php

use CodebarAg\DocuWare\DTO\Documents\DocumentThumbnail;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\Download\DownloadThumbnail;
use CodebarAg\DocuWare\Requests\Documents\Sections\GetAllSectionsFromADocument;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;

it('can download a thumbnail', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $sections = $this->connector->send(new GetAllSectionsFromADocument(
        $fileCabinetId,
        $document->id
    ))->dto();

    $contents = $this->connector->send(new DownloadThumbnail(
        $fileCabinetId,
        $sections->first()->id
    ))->dto();

    expect($contents)->toBeInstanceOf(DocumentThumbnail::class);
    Event::assertDispatched(DocuWareResponseLog::class);

})->group('download');
