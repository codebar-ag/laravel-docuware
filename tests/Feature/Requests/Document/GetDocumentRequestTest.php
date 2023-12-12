<?php

use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Document\GetDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

beforeEach(function () {
    $this->connector = getConnector();
});

it('can show a document', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $getdocument = $this->connector->send(new GetDocumentRequest($fileCabinetId, $document->id))->dto();

    $this->assertInstanceOf(Document::class, $getdocument);

    $this->assertSame($document->id, $getdocument->id);
    $this->assertSame($fileCabinetId, $getdocument->file_cabinet_id);
    Event::assertDispatched(DocuWareResponseLog::class);

});
