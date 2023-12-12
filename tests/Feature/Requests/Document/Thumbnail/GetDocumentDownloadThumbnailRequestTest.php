<?php

use CodebarAg\DocuWare\DTO\DocumentThumbnail;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\Thumbnail\GetDocumentDownloadThumbnailRequest;
use CodebarAg\DocuWare\Requests\Sections\GetSectionsRequest;
use Illuminate\Support\Facades\Event;

it('can download a document thumbnail', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $sections = $this->connector->send(new GetSectionsRequest(
        $fileCabinetId,
        $document->id,
    ))->dto();

    $contents = $this->connector->send(new GetDocumentDownloadThumbnailRequest(
        $fileCabinetId,
        $sections->first()->id,
    ))->dto();

    $this->assertSame('image/png', $contents->mime);
    $this->assertSame(282, strlen($contents->data));

    $this->assertInstanceOf(DocumentThumbnail::class, $contents);

    Event::assertDispatched(DocuWareResponseLog::class);
});
