<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\Sections\GetAllSectionsFromADocument;
use CodebarAg\DocuWare\Requests\Documents\Sections\GetTextshot;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;

it('get textshot for a specific section', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $sectionId = '15850-15497';

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $sections = $this->connector->send(new GetAllSectionsFromADocument(
        $fileCabinetId,
        $document->id
    ))->dto();

    $textshot = $this->connector->send(new GetTextshot(
        $fileCabinetId,
        $sections->first()->id,
    ))->dto();

    expect($textshot->page_count)->toBe(1);
    expect($textshot->pages->first()->content)->toBe(':: fake - file - content ::');

    Event::assertDispatched(DocuWareResponseLog::class);

})->group('requests', 'sections', 'textshot');
