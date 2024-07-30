<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\Sections\GetAllSectionsFromADocument;
use CodebarAg\DocuWare\Requests\Documents\Sections\GetASpecificSection;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;

it('can get a specific section', function () {
    Event::fake();

    $fileCabinetId = env('DOCUWARE_TESTS_FILE_CABINET_ID');
    $dialogId = env('DOCUWARE_TESTS_DIALOG_ID');

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $sections = $this->connector->send(new GetAllSectionsFromADocument(
        $fileCabinetId,
        $document->id
    ))->dto();

    $section = $this->connector->send(new GetASpecificSection(
        $fileCabinetId,
        $sections->first()->id
    ))->dto();

    expect($section->id)->toBe($sections->first()->id);

    Event::assertDispatched(DocuWareResponseLog::class);

})->group('requests', 'sections');
