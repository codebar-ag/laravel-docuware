<?php

use Carbon\Carbon;
use CodebarAg\DocuWare\Connectors\DocuWareStaticConnector;
use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\DTO\Config;
use CodebarAg\DocuWare\DTO\DocumentPaginator;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Exceptions\UnableToSearch;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

beforeEach(function () {
    EnsureValidCookie::check();

    $config = Config::make([
        'url' => config('laravel-docuware.credentials.url'),
        'cookie' => config('laravel-docuware.cookies'),
        'cache_driver' => config('laravel-docuware.configurations.cache.driver'),
        'cache_lifetime_in_seconds' => config('laravel-docuware.configurations.cache.lifetime_in_seconds'),
        'request_timeout_in_seconds' => config('laravel-docuware.timeout'),
    ]);

    $this->connector = new DocuWareStaticConnector($config);
});

it('can search documents', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

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

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

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

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

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

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

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

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

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

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

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

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

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

it('can search documents with null values', function () {
    Event::fake();

    $fileCabinetIds = [
        config('laravel-docuware.tests.file_cabinet_id'),
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


it('can search documents with multiple values', function () {
    Event::fake();

    $fileCabinetIds = [
        config('laravel-docuware.tests.file_cabinet_id'),
    ];

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->fileCabinets($fileCabinetIds)
        ->page(null)
        ->perPage(null)
        ->fulltext(null)
        ->filterIn('DOCUMENT_TYPE', ['Abrechnung', 'Rechnung'])
        ->get();

    $paginator = $this->connector->send($paginatorRequest)->dto();

    $this->assertInstanceOf(DocumentPaginator::class, $paginator);
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('search');
