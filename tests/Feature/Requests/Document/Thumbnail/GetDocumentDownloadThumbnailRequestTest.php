<?php

use CodebarAg\DocuWare\DTO\DocumentThumbnail;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\Thumbnail\GetDocumentDownloadThumbnailRequest;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

beforeEach(function () {
    $this->connector = getConnector();
});

it('can download a document thumbnail', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $contents = $this->connector->send(new GetDocumentDownloadThumbnailRequest(
        $fileCabinetId,
        $document->id,
        $document->id - 1
    ))->dto();

    $this->assertSame('image/png', $contents->mime);
    $this->assertSame(282, strlen($contents->data));

    $this->assertInstanceOf(DocumentThumbnail::class, $contents);

    Event::assertDispatched(DocuWareResponseLog::class);

});
