<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\Sections\DeleteSection;
use CodebarAg\DocuWare\Requests\Documents\Sections\GetAllSectionsFromADocument;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;

it('can delete a specific section', function () {
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

    $deleted = $this->connector->send(new DeleteSection(
        $fileCabinetId,
        $sections->first()->id
    ))->dto();

    expect($deleted)->toBeTrue();

    Event::assertDispatched(DocuWareResponseLog::class);

})->group('requests', 'sections');
