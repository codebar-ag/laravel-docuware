<?php

use Carbon\Carbon;
use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\DTO\Documents\TrashDocumentPaginator;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Exceptions\UnableToSearch;
use Illuminate\Support\Facades\Event;

it('can search documents in trash', function () {
    Event::fake();

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->trashBin()
        ->page(1)
        ->perPage(5)
        ->fulltext('test')
        ->filterDate('DELETEDATETIME', '>=', Carbon::create(2021))
        ->filterDate('DELETEDATETIME', '<', now())
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DELETEDATETIME', 'desc')
        ->get();

    $paginator = $this->connector->send($paginatorRequest)->dto();

    $this->assertInstanceOf(TrashDocumentPaginator::class, $paginator);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('search', 'trash');

it('can\'t search documents by more than two dates in trash', function () {
    Event::fake();

    $this->expectException(UnableToSearch::class);

    $request = (new DocuWare())
        ->searchRequestBuilder()
        ->trashBin()
        ->page(1)
        ->perPage(5)
        ->fulltext('test')
        ->filterDate('DELETEDATETIME', '>=', Carbon::create(2020))
        ->filterDate('DELETEDATETIME', '>=', Carbon::create(2021))
        ->filterDate('DELETEDATETIME', '<', now())
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DELETEDATETIME', 'desc')
        ->get();

    $this->connector->send($request)->dto();
})->group('search', 'trash');

it('can override search documents dates filter by using same operator in trash', function () {
    Event::fake();

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->trashBin()
        ->page(1)
        ->perPage(5)
        ->fulltext('test')
        ->filterDate('DELETEDATETIME', '<=', Carbon::create(2022))
        ->filterDate('DELETEDATETIME', '>=', Carbon::create(2020))
        ->filterDate('DELETEDATETIME', '>=', Carbon::create(2021))
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DELETEDATETIME', 'desc')
        ->get();

    $paginator = $this->connector->send($paginatorRequest)->dto();

    $this->assertInstanceOf(TrashDocumentPaginator::class, $paginator);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('search', 'trash');

it('can override search documents dates filter by using equal operator in trash', function () {
    Event::fake();

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->trashBin()
        ->page(1)
        ->perPage(5)
        ->fulltext('test')
        ->filterDate('DELETEDATETIME', '>=', Carbon::create(2020))
        ->filterDate('DELETEDATETIME', '=', Carbon::create(2021))
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DELETEDATETIME', 'desc')
        ->get();

    $paginator = $this->connector->send($paginatorRequest)->dto();

    $this->assertInstanceOf(TrashDocumentPaginator::class, $paginator);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('search', 'trash');

it('can\'t search documents by diverged date range', function () {
    Event::fake();

    $this->expectException(UnableToSearch::class);

    $request = (new DocuWare())
        ->searchRequestBuilder()
        ->trashBin()
        ->page(1)
        ->perPage(5)
        ->fulltext('test')
        ->filterDate('DELETEDATETIME', '<=', Carbon::create(2020))
        ->filterDate('DELETEDATETIME', '>=', Carbon::create(2021))
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DELETEDATETIME', 'desc')
        ->get();

    $this->connector->send($request)->dto();
})->group('search', 'trash');

it('can search documents dates filter in future in trash', function () {
    Event::fake();

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->trashBin()
        ->page(1)
        ->perPage(5)
        ->fulltext('test')
        ->filterDate('DELETEDATETIME', '>', Carbon::create(2018))
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DELETEDATETIME', 'desc')
        ->get();

    $paginator = $this->connector->send($paginatorRequest)->dto();

    $this->assertInstanceOf(TrashDocumentPaginator::class, $paginator);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('search', 'trash');

it('can search documents dates filter in past in trash', function () {
    Event::fake();

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->trashBin()
        ->page(1)
        ->perPage(5)
        ->fulltext('test')
        ->filterDate('DELETEDATETIME', '<=', Carbon::create(2020))
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DELETEDATETIME', 'desc')
        ->get();

    $paginator = $this->connector->send($paginatorRequest)->dto();

    $this->assertInstanceOf(TrashDocumentPaginator::class, $paginator);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('search', 'trash');

it('can search documents with null values in trash', function () {
    Event::fake();

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->trashBin()
        ->page(null)
        ->perPage(null)
        ->fulltext(null)
        ->filter('DOCUMENT_TYPE', null)
        ->orderBy('DELETEDATETIME', null)
        ->get();

    $paginator = $this->connector->send($paginatorRequest)->dto();

    $this->assertInstanceOf(TrashDocumentPaginator::class, $paginator);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('search', 'trash');
