<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Document\GetDocumentsDownloadRequest;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use Illuminate\Support\Facades\Event;

it('can download multiple documents', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document1 = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $document2 = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $contents = $this->connector->send(new GetDocumentsDownloadRequest(
        $fileCabinetId,
        [$document1->id, $document2->id]
    ))->dto();

    $this->assertSame(478, strlen($contents));
    Event::assertDispatched(DocuWareResponseLog::class);
});
