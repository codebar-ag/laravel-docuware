<?php

use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\SelectLists\GetSelectLists;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;

it('can list values for a select list', function () {
    Event::fake();

    $fileCabinetId = env('DOCUWARE_TESTS_FILE_CABINET_ID');
    $dialogId = env('DOCUWARE_TESTS_DIALOG_ID');
    $fieldName = 'UUID';

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt',
        collect([
            IndexTextDTO::make($fieldName, 'laravel-docuware'),
        ])
    ))->dto();

    $types = $this->connector->send(new GetSelectLists(
        $fileCabinetId,
        $dialogId,
        $fieldName,
    ))->dto();

    $this->assertNotCount(0, $types);
    Event::assertDispatched(DocuWareResponseLog::class);

});
