<?php

use CodebarAg\DocuWare\Connectors\DocuWareWithoutCookieConnector;
use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\DTO\DocumentField;
use CodebarAg\DocuWare\DTO\DocumentIndex;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Document\DeleteDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentCountRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentDownloadRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentPreviewRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentsDownloadRequest;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\PutDocumentFieldsRequest;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

beforeEach(function () {
    EnsureValidCookie::check();

    $this->connector = new DocuWareWithoutCookieConnector(config('docuware.cookies'));
});

it('can preview a document image', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $image = $this->connector->send(new GetDocumentPreviewRequest($fileCabinetId, $document->id))->dto();

    $this->assertSame(9221, strlen($image));
    Event::assertDispatched(DocuWareResponseLog::class);

    $this->connector->send(new DeleteDocumentRequest(
        $fileCabinetId,
        $document->id
    ))->dto();
});

it('can show a document', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');

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

    $this->connector->send(new DeleteDocumentRequest(
        $fileCabinetId,
        $document->id
    ))->dto();
});

it('can update a document value', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $newValue = 'laravel-docuware';

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $response = $this->connector->send(new PutDocumentFieldsRequest(
        $fileCabinetId,
        $document->id,
        ['UUID' => $newValue]
    ))->dto();

    $this->assertSame('laravel-docuware', $response['UUID']);
    Event::assertDispatched(DocuWareResponseLog::class);

    $this->connector->send(new DeleteDocumentRequest(
        $fileCabinetId,
        $document->id
    ))->dto();
});

it('can update multiple document values', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $values = [
        'UUID' => 'laravel-docuware',
        'DOCUMENT_LABEL' => 'laravel-docuware-2',
    ];

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $response = $this->connector->send(new PutDocumentFieldsRequest(
        $fileCabinetId,
        $document->id,
        $values,
        true
    ))->dto();

    $this->assertSame('laravel-docuware', $response['UUID']);
    $this->assertSame('laravel-docuware-2', $response['DOCUMENT_LABEL']);

    Event::assertDispatched(DocuWareResponseLog::class);

    $this->connector->send(new DeleteDocumentRequest(
        $fileCabinetId,
        $document->id
    ))->dto();
});

it('can download multiple documents', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');

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

    $this->connector->send(new DeleteDocumentRequest(
        $fileCabinetId,
        $document1->id
    ))->dto();

    $this->connector->send(new DeleteDocumentRequest(
        $fileCabinetId,
        $document2->id
    ))->dto();
});

it('can download a document', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $contents = $this->connector->send(new GetDocumentDownloadRequest(
        $fileCabinetId,
        $document->id
    ))->dto();

    $this->assertSame(strlen('::fake-file-content::'), strlen($contents));
    Event::assertDispatched(DocuWareResponseLog::class);

    $this->connector->send(new DeleteDocumentRequest(
        $fileCabinetId,
        $document->id
    ))->dto();
});

it('can get a total count of documents', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $dialogId = config('docuware.tests.dialog_id');

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $count = $this->connector->send(new GetDocumentCountRequest(
        $fileCabinetId,
        $dialogId
    ))->dto();

    $this->assertSame(1, $count);
    Event::assertDispatched(DocuWareResponseLog::class);

    $this->connector->send(new DeleteDocumentRequest(
        $fileCabinetId,
        $document->id
    ))->dto();
});

it('can upload document with index values and delete it', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $fileContent = '::fake-file-content::';
    $fileName = 'example.txt';

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        $fileContent,
        $fileName,
        collect([
            DocumentIndex::make('DOCUMENT_LABEL', '::text::'),
        ]),
    ))->dto();

    $this->connector->send(new DeleteDocumentRequest(
        $fileCabinetId,
        $document->id,
    ))->dto();

    $this->assertInstanceOf(Document::class, $document);
    $this->assertSame('example', $document->title);
    tap($document->fields['DOCUMENT_LABEL'], function (DocumentField $field) {
        $this->assertSame($field->name, 'DOCUMENT_LABEL');
        $this->assertSame($field->type, 'String');
        $this->assertSame($field->value, '::text::');
    });
    Event::assertDispatched(DocuWareResponseLog::class);
});
