<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Document\GetDocumentPreviewRequest;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use Illuminate\Support\Facades\Event;

it('can preview a document image', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $image = $this->connector->send(new GetDocumentPreviewRequest($fileCabinetId, $document->id))->dto();

    $this->assertSame(9221, strlen($image));
    Event::assertDispatched(DocuWareResponseLog::class);

});
