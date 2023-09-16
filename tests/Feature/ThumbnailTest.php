<?php

use CodebarAg\DocuWare\Connectors\DocuWareWithoutCookieConnector;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Document\DeleteDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\Thumbnail\GetDocumentDownloadThumbnailRequest;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

beforeEach(function () {
    EnsureValidCookie::check();

    $this->connector = new DocuWareWithoutCookieConnector(config('docuware.cookies'));
});

it('can download a document thumbnail', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');

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
    Event::assertDispatched(DocuWareResponseLog::class);

    $this->connector->send(new DeleteDocumentRequest(
        $fileCabinetId,
        $document->id
    ))->dto();
});
