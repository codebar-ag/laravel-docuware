<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\General\GetTotalNumberOfDocuments;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('can get a total count of documents', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    Sleep::for(2)->seconds(); // Wait for the document to be processed

    $count = $this->connector->send(new GetTotalNumberOfDocuments(
        $fileCabinetId,
        $dialogId
    ))->dto();

    $this->assertIsInt($count);

    $this->assertSame(1, $count);

    Event::assertDispatched(DocuWareResponseLog::class);

});
