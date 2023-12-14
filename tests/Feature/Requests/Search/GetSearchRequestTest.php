<?php

use Carbon\Carbon;
use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\DTO\DocumentIndex\IndexTextDTO;
use CodebarAg\DocuWare\DTO\DocumentPaginator;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Exceptions\UnableToSearch;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use Illuminate\Support\Facades\Event;

it('can search documents', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->fileCabinet($fileCabinetId)
        ->dialog($dialogId)
        ->fulltext('test')
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2021))
        ->filterDate('DWSTOREDATETIME', '<', now())
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    $paginator = $this->connector->paginate($paginatorRequest);

    $paginator->setPerPageLimit(5);
    $paginator->getSinglePage(1);

    $documents = collect();

    foreach ($paginator as $response) {
        ray($response->dto());

        $this->assertCount(0, $response->dto());

        $documents->push($response->dto());
    }

    $this->assertCount(0, $documents->flatten());
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
        ->fulltext('test')
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2020))
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2021))
        ->filterDate('DWSTOREDATETIME', '<', now())
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    $paginator = $this->connector->paginate($request);

    $paginator->setPerPageLimit(5);
    $paginator->getSinglePage(1);

    $documents = collect();

    foreach ($paginator as $response) {
        ray($response->dto());

        $this->assertCount(2, $response->dto());

        $documents->push($response->dto());
    }

    $this->assertCount(4, $documents->flatten());
})->group('search');

it('can override search documents dates filter by using same operator', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->fileCabinet($fileCabinetId)
        ->dialog($dialogId)
        ->fulltext('test')
        ->filterDate('DWSTOREDATETIME', '<=', Carbon::create(2022))
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2020))
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2021))
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    $paginator = $this->connector->paginate($paginatorRequest);

    $paginator->setPerPageLimit(5);
    $paginator->getSinglePage(1);

    $documents = collect();

    foreach ($paginator as $response) {
        ray($response->dto());

        $this->assertCount(0, $response->dto());

        $documents->push($response->dto());
    }

    $this->assertCount(0, $documents->flatten());
})->group('search');

it('can override search documents dates filter by using equal operator', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->fileCabinet($fileCabinetId)
        ->dialog($dialogId)
        ->fulltext('test')
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2020))
        ->filterDate('DWSTOREDATETIME', '=', Carbon::create(2021))
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    $paginator = $this->connector->paginate($paginatorRequest);

    $paginator->setPerPageLimit(5);
    $paginator->getSinglePage(1);

    $documents = collect();

    foreach ($paginator as $response) {
        ray($response->dto());

        $this->assertCount(0, $response->dto());

        $documents->push($response->dto());
    }

    $this->assertCount(0, $documents->flatten());
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
        ->fulltext('test')
        ->filterDate('DWSTOREDATETIME', '<=', Carbon::create(2020))
        ->filterDate('DWSTOREDATETIME', '>=', Carbon::create(2021))
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    $paginator = $this->connector->paginate($request);

    $paginator->setPerPageLimit(5);
    $paginator->getSinglePage(1);

    $documents = collect();

    foreach ($paginator as $response) {
        ray($response->dto());

        $this->assertCount(2, $response->dto());

        $documents->push($response->dto());
    }

    $this->assertCount(4, $documents->flatten());
})->group('search');

it('can search documents dates filter in future', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->fileCabinet($fileCabinetId)
        ->dialog($dialogId)
        ->fulltext('test')
        ->filterDate('DWSTOREDATETIME', '>', Carbon::create(2018))
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    $paginator = $this->connector->paginate($paginatorRequest);

    $paginator->setPerPageLimit(5);
    $paginator->getSinglePage(1);

    $documents = collect();

    foreach ($paginator as $response) {
        ray($response->dto());

        $this->assertCount(0, $response->dto());

        $documents->push($response->dto());
    }

    $this->assertCount(0, $documents->flatten());
})->group('search');

it('can search documents dates filter in past', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->fileCabinet($fileCabinetId)
        ->dialog($dialogId)
        ->fulltext('test')
        ->filterDate('DWSTOREDATETIME', '<=', Carbon::create(2020))
        ->filter('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    $paginator = $this->connector->paginate($paginatorRequest);

    $paginator->setPerPageLimit(5);
    $paginator->getSinglePage(1);

    $documents = collect();

    foreach ($paginator as $response) {
        ray($response->dto());

        $this->assertCount(0, $response->dto());

        $documents->push($response->dto());
    }

    $this->assertCount(0, $documents->flatten());
})->group('search');

it('can search documents with null values', function () {
    Event::fake();

    $fileCabinetIds = [
        config('laravel-docuware.tests.file_cabinet_id'),
    ];

    $paginatorRequest = (new DocuWare())
        ->searchRequestBuilder()
        ->fileCabinets($fileCabinetIds)
        ->fulltext(null)
        ->filter('DOCUMENT_TYPE', null)
        ->orderBy('DWSTOREDATETIME', null)
        ->get();

    $paginator = $this->connector->paginate($paginatorRequest);

    $documents = collect();

    foreach ($paginator as $response) {
        ray($response->dto());

        $this->assertCount(0, $response->dto());

        $documents->push($response->dto());
    }

    $this->assertCount(0, $documents->flatten());
})->group('search');

it('can search documents with multiple values', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $fileContent = '::fake-file-content::';
    $fileName = 'example.txt';

    $documentOne = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        $fileContent,
        $fileName,
        collect([
            IndexTextDTO::make('DOCUMENT_LABEL', '::text::'),
            IndexTextDTO::make('DOCUMENT_TYPE', 'Abrechnung'),
        ]),
    ))->dto();

    $documentTwo = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        $fileContent,
        $fileName,
        collect([
            IndexTextDTO::make('DOCUMENT_LABEL', '::text::'),
            IndexTextDTO::make('DOCUMENT_TYPE', 'Rechnung'),
        ]),
    ))->dto();

    $documentThree = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        $fileContent,
        $fileName,
        collect([
            IndexTextDTO::make('DOCUMENT_LABEL', '::text::'),
            IndexTextDTO::make('DOCUMENT_TYPE', 'EtwasAnderes'),
        ]),
    ))->dto();

    // Should filter down to documentOne and documentTwo. documentThree should be filtered out.
    $paginatorRequestBothDocuments = (new DocuWare())
        ->searchRequestBuilder()
        ->fileCabinets([$fileCabinetId])
        ->fulltext(null)
        ->filterIn('DOCUMENT_TYPE', ['Abrechnung', 'Rechnung'])
        ->get();

    $paginator = $this->connector->paginate($paginatorRequestBothDocuments);

    $documents = collect();

    foreach ($paginator as $response) {
        ray($response->dto());

        $this->assertCount(2, $response->dto());

        $documents->push($response->dto());
    }

    $this->assertCount(2, $documents->flatten());
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('search');

it('can search and get paginated results', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $fileContent = '::fake-file-content::';
    $fileName = 'example.txt';

    for ($i = 0; $i < 4; $i++) {
        $this->connector->send(new PostDocumentRequest(
            config('laravel-docuware.tests.file_cabinet_id'),
            '::fake-file-content::',
            'example.txt'
        ))->dto();
    }

    $paginatorRequestBothDocuments = (new DocuWare())
        ->searchRequestBuilder()
        ->fileCabinets([$fileCabinetId])
        ->fulltext(null)
        ->get();

    $paginator = $this->connector->paginate($paginatorRequestBothDocuments);

    $paginator->setPerPageLimit(2);

    $documents = collect();

    foreach ($paginator as $response) {
        ray($response->dto());

        $this->assertCount(2, $response->dto());

        $documents->push($response->dto());
    }

    $this->assertCount(4, $documents->flatten());
    Event::assertDispatched(DocuWareResponseLog::class);
})->group('search');
