<?php

namespace CodebarAg\DocuWare\Tests\Feature;

use Carbon\Carbon;
use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\DTO\DocumentField;
use CodebarAg\DocuWare\DTO\DocumentIndex;
use CodebarAg\DocuWare\DTO\DocumentPaginator;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

// fileCabinet = '4ca593b2-c19d-4399-96e6-c90168dbaa97';
// dialog = '4fc78419-37f4-409b-ab08-42e5cecdee92';

it('it_can_list_file_cabinets', function () {
    Event::fake();

    $fileCabinets = (new DocuWare())->getFileCabinets();

    $this->assertInstanceOf(Collection::class, $fileCabinets);
    $this->assertNotCount(0, $fileCabinets);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('docuware');

it('it_can_list_fields_for_a_file_cabinet', function () {
    Event::fake();

    $fileCabinetId = '4ca593b2-c19d-4399-96e6-c90168dbaa97';

    $fields = (new DocuWare())->getFields($fileCabinetId);

    $this->assertInstanceOf(Collection::class, $fields);
    $this->assertNotCount(0, $fields);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('docuware');

it('it_can_list_values_for_a_select_list', function () {
    Event::fake();

    $fileCabinetId = '4ca593b2-c19d-4399-96e6-c90168dbaa97';
    $dialogId = '4fc78419-37f4-409b-ab08-42e5cecdee92';
    $fieldName = 'UUID';

    $types = (new DocuWare())->getSelectList(
        $fileCabinetId,
        $dialogId,
        $fieldName,
    );

    $this->assertNotCount(0, $types);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('docuware');

it('it_can_list_dialogs_for_a_file_cabinet', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');

    $dialogs = (new DocuWare())->getDialogs($fileCabinetId);

    $this->assertInstanceOf(Collection::class, $dialogs);
    $this->assertNotCount(0, $dialogs);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('docuware');

it('it_can_preview_a_document_image', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $documentId = config('docuware.tests.document_id');

    $image = (new DocuWare())->getDocumentPreview(
        $fileCabinetId,
        $documentId,
    );

    $this->assertSame(config('docuware.tests.document_file_size_preview'), strlen($image));
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('docuware');

it('it_can_show_a_document', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $documentId = config('docuware.tests.document_id');

    $document = (new DocuWare())->getDocument(
        $fileCabinetId,
        $documentId,
    );

    $this->assertInstanceOf(Document::class, $document);
    $this->assertSame($documentId, $document->id);
    $this->assertSame($fileCabinetId, $document->file_cabinet_id);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('docuware');

it('it_can_update_a_document_value', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $documentId = config('docuware.tests.document_id');
    $fieldName = config('docuware.tests.field_name');
    $newValue = 'laravel-docuware';

    $response = (new DocuWare())->updateDocumentValue(
        $fileCabinetId,
        $documentId,
        $fieldName,
        $newValue,
    );

    $this->assertSame('laravel-docuware', $response);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('docuware', 'test');

it('it_can_download_multiple_documents', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $documentIds = config('docuware.tests.document_ids');

    $contents = (new DocuWare())->downloadDocuments(
        $fileCabinetId,
        $documentIds,
    );

    $this->assertSame(config('docuware.tests.documents_file_size'), strlen($contents));
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('docuware');

it('it_can_download_a_document', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $documentId = config('docuware.tests.document_id');

    $contents = (new DocuWare())->downloadDocument(
        $fileCabinetId,
        $documentId,
    );

    $this->assertSame(config('docuware.tests.document_file_size'), strlen($contents));
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('docuware');

it('it_can_search_documents', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $dialogId = config('docuware.tests.dialog_id');

    $paginator = (new DocuWare())
        ->search()
        ->fileCabinet($fileCabinetId)
        ->dialog($dialogId)
        ->page(1)
        ->perPage(5)
        ->fulltext('test')
        ->dateFrom(Carbon::create(2021))
        ->dateUntil(now())
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    $this->assertInstanceOf(DocumentPaginator::class, $paginator);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('docuware');

it('it_can_upload_document_with_index_values_and_delete_it', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $fileContent = '::fake-file-content::';
    $fileName = 'example.txt';

    $document = (new DocuWare())->uploadDocument(
        $fileCabinetId,
        $fileContent,
        $fileName,
        collect([
            DocumentIndex::make('DOCUMENT_LABEL', '::text::'),
        ]),
    );
    (new DocuWare())->deleteDocument($fileCabinetId, $document->id);

    $this->assertInstanceOf(Document::class, $document);
    $this->assertSame('example', $document->title);
    tap($document->fields['DOCUMENT_LABEL'], function (DocumentField $field) {
        $this->assertSame($field->name, 'DOCUMENT_LABEL');
        $this->assertSame($field->type, 'String');
        $this->assertSame($field->value, '::text::');
    });
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('docuware');

it('it_can_create_encrypted_url_for_a_document_in_a_file_cabinet', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $documentId = config('docuware.tests.document_id');

    $url = (new DocuWare())
        ->url()
        ->fileCabinet($fileCabinetId)
        ->document($documentId)
        ->validUntil(now()->addMinute())
        ->make();

    $endpoint = sprintf(
        '%s/DocuWare/Platform/WebClient/Integration?ep=',
        config('docuware.credentials.url'),
    );

    $this->assertStringStartsWith(
        $endpoint,
        $url,
    );
})->group('docuware');

it('it_can_search_documents_with_null_values', function () {
    Event::fake();

    $fileCabinetIds = [
        config('docuware.tests.file_cabinet_id'),
    ];

    $paginator = (new DocuWare())
        ->search()
        ->fileCabinets($fileCabinetIds)
        ->page(null)
        ->perPage(null)
        ->fulltext(null)
        ->dateFrom(null)
        ->dateUntil(null)
        ->filter('DOCUMENT_TYPE', null)
        ->orderBy('DWSTOREDATETIME', null)
        ->get();

    $this->assertInstanceOf(DocumentPaginator::class, $paginator);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('docuware');

it('it_can_create_encrypted_url_for_a_document_in_a_basket', function () {
    Event::fake();

    $basketId = config('docuware.tests.basket_id');
    $documentId = config('docuware.tests.document_id');

    $url = (new DocuWare())
        ->url()
        ->basket($basketId)
        ->document($documentId)
        ->validUntil(now()->addMinute())
        ->make();

    $endpoint = sprintf(
        '%s/DocuWare/Platform/WebClient/Integration?ep=',
        config('docuware.credentials.url'),
    );

    $this->assertStringStartsWith(
        $endpoint,
        $url,
    );
})->group('docuware');
