<?php

use Carbon\Carbon;
use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTextDTO;
use CodebarAg\DocuWare\DTO\Documents\DocumentPaginator;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Exceptions\UnableToSearch;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;

it('can search documents', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

    $paginatorRequest = (new DocuWare)
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

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

    $this->expectException(UnableToSearch::class);

    $request = (new DocuWare)
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

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

    $paginatorRequest = (new DocuWare)
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

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

    $paginatorRequest = (new DocuWare)
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

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

    $this->expectException(UnableToSearch::class);

    $request = (new DocuWare)
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

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

    $paginatorRequest = (new DocuWare)
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

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

    $paginatorRequest = (new DocuWare)
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

it('can search documents with null values', function () {
    Event::fake();

    $fileCabinetIds = [
        config('laravel-docuware.tests.file_cabinet_id'),
    ];

    $paginatorRequest = (new DocuWare)
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

it('can search documents with multiple values', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $fileContent = '::fake-file-content::';
    $fileName = 'example.txt';

    $documentOne = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        $fileContent,
        $fileName,
        collect([
            IndexTextDTO::make('DOCUMENT_LABEL', '::text::'),
            IndexTextDTO::make('DOCUMENT_TYPE', 'Abrechnung'),
        ]),
    ))->dto();

    $documentTwo = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        $fileContent,
        $fileName,
        collect([
            IndexTextDTO::make('DOCUMENT_LABEL', '::text::'),
            IndexTextDTO::make('DOCUMENT_TYPE', 'Rechnung'),
        ]),
    ))->dto();

    $documentThree = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        $fileContent,
        $fileName,
        collect([
            IndexTextDTO::make('DOCUMENT_LABEL', '::text::'),
            IndexTextDTO::make('DOCUMENT_TYPE', 'EtwasAnderes'),
        ]),
    ))->dto();

    // Should filter down to documentOne and documentTwo. documentThree should be filtered out.
    $paginatorRequestBothDocuments = (new DocuWare)
        ->searchRequestBuilder()
        ->fileCabinets([$fileCabinetId])
        ->page(null)
        ->perPage(null)
        ->fulltext(null)
        ->filterIn('DOCUMENT_TYPE', ['Abrechnung', 'Rechnung'])
        ->get();

    $paginator = $this->connector->send($paginatorRequestBothDocuments)->dto();

    $this->assertInstanceOf(DocumentPaginator::class, $paginator);
    $this->assertCount(2, $paginator->documents);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('search');
