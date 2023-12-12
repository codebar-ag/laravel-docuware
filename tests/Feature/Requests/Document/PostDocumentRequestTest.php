<?php

use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\DTO\DocumentField;
use CodebarAg\DocuWare\DTO\DocumentIndex\IndexTextDTO;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use Illuminate\Support\Facades\Event;

it('can upload document without file name and file content and delete it', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        null,
        null,
        collect([
            IndexTextDTO::make('DOCUMENT_LABEL', '::data-entry::'),
        ]),
    ))->dto();

    $this->assertInstanceOf(Document::class, $document);

    tap($document->fields['DOCUMENT_LABEL'], function (DocumentField $field) {
        $this->assertSame($field->name, 'DOCUMENT_LABEL');
        $this->assertSame($field->type, 'String');
        $this->assertSame($field->value, '::data-entry::');
    });
    Event::assertDispatched(DocuWareResponseLog::class);
});

it('can upload document with index values and delete it', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $fileContent = '::fake-file-content::';
    $fileName = 'example.txt';

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        $fileContent,
        $fileName,
        collect([
            IndexTextDTO::make('DOCUMENT_LABEL', '::text::'),
        ]),
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
