<?php

use CodebarAg\DocuWare\DTO\DocumentIndex\IndexTextDTO;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use CodebarAg\DocuWare\Requests\SelectList\GetSelectListRequest;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

beforeEach(function () {
    $this->connector = getConnector();
});

it('can list values for a select list', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');
    $fieldName = 'UUID';

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt',
        collect([
            IndexTextDTO::make($fieldName, 'laravel-docuware'),
        ])
    ))->dto();

    $types = $this->connector->send(new GetSelectListRequest(
        $fileCabinetId,
        $dialogId,
        $fieldName,
    ))->dto();

    $this->assertNotCount(0, $types);
    Event::assertDispatched(DocuWareResponseLog::class);

});
