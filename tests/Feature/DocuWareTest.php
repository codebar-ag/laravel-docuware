<?php

namespace CodebarAg\DocuWare\Tests\Feature;

use Carbon\Carbon;
use CodebarAg\DocuWare\Connectors\DocuWareWithoutCookieConnector;
use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\DTO\DocumentField;
use CodebarAg\DocuWare\DTO\DocumentIndex;
use CodebarAg\DocuWare\DTO\DocumentPaginator;
use CodebarAg\DocuWare\DTO\Organization;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Exceptions\UnableToSearch;
use CodebarAg\DocuWare\Requests\Document\DeleteDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentCountRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentDownloadRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentPreviewRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentsDownloadRequest;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\PutDocumentFieldsRequest;
use CodebarAg\DocuWare\Requests\Document\Thumbnail\GetDocumentDownloadThumbnailRequest;
use CodebarAg\DocuWare\Requests\GetDialogsRequest;
use CodebarAg\DocuWare\Requests\GetFieldsRequest;
use CodebarAg\DocuWare\Requests\GetFileCabinetsRequest;
use CodebarAg\DocuWare\Requests\GetSelectListRequest;
use CodebarAg\DocuWare\Requests\Organization\GetOrganizationRequest;
use CodebarAg\DocuWare\Requests\Organization\GetOrganizationsRequest;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Saloon\Exceptions\InvalidResponseClassException;
use Saloon\Exceptions\PendingRequestException;

uses()->group('docuware');

beforeEach(function () {
    EnsureValidCookie::check();

    $this->connector = new DocuWareWithoutCookieConnector(config('docuware.cookies'));
});

it('can list organizations', function () {
    Event::fake();

    $organizations = $this->connector->send(new GetOrganizationsRequest())->dto();

    $this->assertInstanceOf(Collection::class, $organizations);
    $this->assertNotCount(0, $organizations);
    Event::assertDispatched(DocuWareResponseLog::class);
});

it('can get an organization', function () {
    Event::fake();

    $orgID = config('docuware.tests.organization_id');

    $organization = $this->connector->send(new GetOrganizationRequest($orgID))->dto();

    $this->assertInstanceOf(Organization::class, $organization);
    Event::assertDispatched(DocuWareResponseLog::class);
});

it('can list file cabinets', function () {
    Event::fake();

    $fileCabinets = $this->connector->send(new GetFileCabinetsRequest())->dto();

    $this->assertInstanceOf(Collection::class, $fileCabinets);
    $this->assertNotCount(0, $fileCabinets);
    Event::assertDispatched(DocuWareResponseLog::class);
});

it('can list fields for a file cabinet', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');

    $fields = $this->connector->send(new GetFieldsRequest($fileCabinetId))->dto();

    $this->assertInstanceOf(Collection::class, $fields);
    $this->assertNotCount(0, $fields);
    Event::assertDispatched(DocuWareResponseLog::class);
});

it('can list values for a select list', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $dialogId = config('docuware.tests.dialog_id');
    $fieldName = 'UUID';

    $types = $this->connector->send(new GetSelectListRequest(
        $fileCabinetId,
        $dialogId,
        $fieldName,
    ))->dto();

    $this->assertNotCount(0, $types);
    Event::assertDispatched(DocuWareResponseLog::class);
})->skip();

it('can list dialogs for a file cabinet', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');

    $dialogs = $this->connector->send(new GetDialogsRequest($fileCabinetId))->dto();

    $this->assertInstanceOf(Collection::class, $dialogs);
    $this->assertNotCount(0, $dialogs);
    Event::assertDispatched(DocuWareResponseLog::class);
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
    $fieldName = config('docuware.tests.field_name');
    $newValue = 'laravel-docuware';

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $response = $this->connector->send(new PutDocumentFieldsRequest(
        $fileCabinetId,
        $document->id,
        [$fieldName => $newValue]
    ))->dto();

    $this->assertSame('laravel-docuware', $response[$fieldName]);
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
        config('docuware.tests.field_name') => 'laravel-docuware',
        config('docuware.tests.field_name_2') => 'laravel-docuware-2',
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

    $this->assertSame('laravel-docuware', $response[config('docuware.tests.field_name')]);
    $this->assertSame('laravel-docuware-2', $response[config('docuware.tests.field_name_2')]);

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

it('can download a document thumbnail', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $documentId = config('docuware.tests.document_id');
    $section = config('docuware.tests.section');

    $contents = $this->connector->send(new GetDocumentDownloadThumbnailRequest(
        $fileCabinetId,
        $documentId,
        $section,
    ))->dto();

    $this->assertSame(config('docuware.tests.document_thumbnail_mime_type'), $contents->mime);
    $this->assertSame(config('docuware.tests.document_thumbnail_file_size'), strlen($contents->data));
    Event::assertDispatched(DocuWareResponseLog::class);
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

it(/**
 * @throws InvalidResponseClassException
 * @throws \ReflectionException
 * @throws PendingRequestException
 */ 'can search documents', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $dialogId = config('docuware.tests.dialog_id');

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->fileCabinet($fileCabinetId)
        ->dialog($dialogId)
        ->page(1)
        ->perPage(5)
        ->fulltext('test')
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2021))
        ->filterDate('DWSTOREDATETIME', '<', now())
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    $paginator = $this->connector->send($paginatorRequest)->dto();

    $this->assertInstanceOf(DocumentPaginator::class, $paginator);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('search');

it('can\'t search documents by more than two dates', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $dialogId = config('docuware.tests.dialog_id');

    $this->expectException(UnableToSearch::class);

    $request = (new DocuWare())
        ->searchRequestBuilder()
        ->fileCabinet($fileCabinetId)
        ->dialog($dialogId)
        ->page(1)
        ->perPage(5)
        ->fulltext('test')
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2020))
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2021))
        ->filterDate('DWSTOREDATETIME', '<', now())
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    $this->connector->send($request)->dto();
})->group('search');

it('can override search documents dates filter by using same operator', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $dialogId = config('docuware.tests.dialog_id');

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->fileCabinet($fileCabinetId)
        ->dialog($dialogId)
        ->page(1)
        ->perPage(5)
        ->fulltext('test')
        ->filterDate('DWSTOREDATETIME', '<=', Carbon::create(2022))
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2020))
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2021))
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    $paginator = $this->connector->send($paginatorRequest)->dto();

    $this->assertInstanceOf(DocumentPaginator::class, $paginator);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('search');

it('can override search documents dates filter by using equal operator', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $dialogId = config('docuware.tests.dialog_id');

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->fileCabinet($fileCabinetId)
        ->dialog($dialogId)
        ->page(1)
        ->perPage(5)
        ->fulltext('test')
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2020))
        ->filterDate('DWSTOREDATETIME', '=', Carbon::create(2021))
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    $paginator = $this->connector->send($paginatorRequest)->dto();

    $this->assertInstanceOf(DocumentPaginator::class, $paginator);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('search');

it('can\'t search documents by diverged date range', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $dialogId = config('docuware.tests.dialog_id');

    $this->expectException(UnableToSearch::class);

    $request = (new DocuWare())
        ->searchRequestBuilder()
        ->fileCabinet($fileCabinetId)
        ->dialog($dialogId)
        ->page(1)
        ->perPage(5)
        ->fulltext('test')
        ->filterDate('DWSTOREDATETIME', '<=', Carbon::create(2020))
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2021))
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    $this->connector->send($request)->dto();
})->group('search');

it('can search documents dates filter in future', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $dialogId = config('docuware.tests.dialog_id');

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->fileCabinet($fileCabinetId)
        ->dialog($dialogId)
        ->page(1)
        ->perPage(5)
        ->fulltext('test')
        ->filterDate('DWSTOREDATETIME', '>', Carbon::create(2018))
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    $paginator = $this->connector->send($paginatorRequest)->dto();

    $this->assertInstanceOf(DocumentPaginator::class, $paginator);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('search');

it('can search documents dates filter in past', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $dialogId = config('docuware.tests.dialog_id');

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->fileCabinet($fileCabinetId)
        ->dialog($dialogId)
        ->page(1)
        ->perPage(5)
        ->fulltext('test')
        ->filterDate('DWSTOREDATETIME', '<=', Carbon::create(2020))
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    $paginator = $this->connector->send($paginatorRequest)->dto();

    $this->assertInstanceOf(DocumentPaginator::class, $paginator);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('search');

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

it('can create encrypted url for a document in a file cabinet', function () {
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
});

it('can search documents with null values', function () {
    Event::fake();

    $fileCabinetIds = [
        config('docuware.tests.file_cabinet_id'),
    ];

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->fileCabinets($fileCabinetIds)
        ->page(null)
        ->perPage(null)
        ->fulltext(null)
        ->filter('DOCUMENT_TYPE', null)
        ->orderBy('DWSTOREDATETIME', null)
        ->get();

    $paginator = $this->connector->send($paginatorRequest)->dto();

    $this->assertInstanceOf(DocumentPaginator::class, $paginator);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('search');

it('can create encrypted url for a document in a basket', function () {
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
});
