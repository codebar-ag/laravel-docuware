<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Document\GetDocumentCountRequest;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

beforeEach(function () {
    $this->connector = getConnector();
});

it('can get a total count of documents', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $count = $this->connector->send(new GetDocumentCountRequest(
        $fileCabinetId,
        $dialogId
    ))->dto();

    $this->assertIsInt($count);

    $this->assertSame(1, $count);

    Event::assertDispatched(DocuWareResponseLog::class);

});
