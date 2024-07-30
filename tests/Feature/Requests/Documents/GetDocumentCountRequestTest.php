<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\General\GetTotalNumberOfDocuments;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;

it('can get a total count of documents', function () {
    Event::fake();

    $fileCabinetId = env('DOCUWARE_TESTS_FILE_CABINET_ID');
    $dialogId = env('DOCUWARE_TESTS_DIALOG_ID');

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $count = $this->connector->send(new GetTotalNumberOfDocuments(
        $fileCabinetId,
        $dialogId
    ))->dto();

    $this->assertIsInt($count);

    $this->assertSame(1, $count);

    Event::assertDispatched(DocuWareResponseLog::class);

});
