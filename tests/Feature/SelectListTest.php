<?php

use CodebarAg\DocuWare\Connectors\DocuWareWithoutCookieConnector;
use CodebarAg\DocuWare\DTO\DocumentIndex;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Document\DeleteDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use CodebarAg\DocuWare\Requests\SelectList\GetSelectListRequest;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

beforeEach(function () {
    EnsureValidCookie::check();

    $this->connector = new DocuWareWithoutCookieConnector(config('docuware.cookies'));
});

it('can list values for a select list', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $dialogId = config('docuware.tests.dialog_id');
    $fieldName = 'UUID';

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt',
        collect([
            DocumentIndex::make($fieldName, 'laravel-docuware'),
        ])
    ))->dto();

    $types = $this->connector->send(new GetSelectListRequest(
        $fileCabinetId,
        $dialogId,
        $fieldName,
    ))->dto();

    $this->assertNotCount(0, $types);
    Event::assertDispatched(DocuWareResponseLog::class);

    $this->connector->send(new DeleteDocumentRequest(
        $fileCabinetId,
        $document->id
    ))->dto();
});
