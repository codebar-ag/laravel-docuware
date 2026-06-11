<?php

use Carbon\Carbon;
use CodebarAg\DocuWare\Data\Documents\DocumentPageData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Exceptions\UnableToSearch;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('can search documents', function () {
    Event::fake();

    $dialogId = sandboxSearchDialogId($this->cabinet);

    $page = DocuWare::documents($this->cabinet)
        ->search()
        ->dialog($dialogId)
        ->page(1)
        ->perPage(5)
        ->fullText('test')
        ->whereDate('DWSTOREDATETIME', '>=', Carbon::create(2021))
        ->whereDate('DWSTOREDATETIME', '<', now())
        ->where('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    expect($page)->toBeInstanceOf(DocumentPageData::class);

    Event::assertDispatched(ResponseReceived::class);
});

it('can\'t search documents by more than two dates', function () {
    $this->expectException(UnableToSearch::class);

    DocuWare::documents($this->cabinet)
        ->search()
        ->whereDate('DWSTOREDATETIME', '>=', Carbon::create(2020))
        ->whereDate('DWSTOREDATETIME', '<=', Carbon::create(2022))
        ->whereDate('DWSTOREDATETIME', '<', now());
});

it('can override search documents dates filter by using same operator', function () {
    Event::fake();

    $dialogId = sandboxSearchDialogId($this->cabinet);

    $page = DocuWare::documents($this->cabinet)
        ->search()
        ->dialog($dialogId)
        ->page(1)
        ->perPage(5)
        ->fullText('test')
        ->whereDate('DWSTOREDATETIME', '<=', Carbon::create(2022))
        ->whereDate('DWSTOREDATETIME', '>=', Carbon::create(2020))
        ->whereDate('DWSTOREDATETIME', '>=', Carbon::create(2021))
        ->where('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    expect($page)->toBeInstanceOf(DocumentPageData::class);

    Event::assertDispatched(ResponseReceived::class);
});

it('can override search documents dates filter by using equal operator', function () {
    Event::fake();

    $dialogId = sandboxSearchDialogId($this->cabinet);

    $page = DocuWare::documents($this->cabinet)
        ->search()
        ->dialog($dialogId)
        ->page(1)
        ->perPage(5)
        ->fullText('test')
        ->whereDate('DWSTOREDATETIME', '>=', Carbon::create(2020))
        ->whereDate('DWSTOREDATETIME', '=', Carbon::create(2021))
        ->where('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    expect($page)->toBeInstanceOf(DocumentPageData::class);

    Event::assertDispatched(ResponseReceived::class);
});

it('can\'t search documents by diverged date range', function () {
    $dialogId = sandboxSearchDialogId($this->cabinet);

    $this->expectException(UnableToSearch::class);

    DocuWare::documents($this->cabinet)
        ->search()
        ->dialog($dialogId)
        ->page(1)
        ->perPage(5)
        ->fullText('test')
        ->whereDate('DWSTOREDATETIME', '<=', Carbon::create(2020))
        ->whereDate('DWSTOREDATETIME', '>=', Carbon::create(2021))
        ->where('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();
});

it('can search documents dates filter in future', function () {
    Event::fake();

    $dialogId = sandboxSearchDialogId($this->cabinet);

    $page = DocuWare::documents($this->cabinet)
        ->search()
        ->dialog($dialogId)
        ->page(1)
        ->perPage(5)
        ->fullText('test')
        ->whereDate('DWSTOREDATETIME', '>', Carbon::create(2018))
        ->where('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    expect($page)->toBeInstanceOf(DocumentPageData::class);

    Event::assertDispatched(ResponseReceived::class);
});

it('can search documents dates filter in past', function () {
    Event::fake();

    $dialogId = sandboxSearchDialogId($this->cabinet);

    $page = DocuWare::documents($this->cabinet)
        ->search()
        ->dialog($dialogId)
        ->page(1)
        ->perPage(5)
        ->fullText('test')
        ->whereDate('DWSTOREDATETIME', '<=', Carbon::create(2020))
        ->where('DOCUMENT_TYPE', 'Abrechnung')
        ->orderBy('DWSTOREDATETIME', 'desc')
        ->get();

    expect($page)->toBeInstanceOf(DocumentPageData::class);

    Event::assertDispatched(ResponseReceived::class);
});

it('can search documents with null values', function () {
    Event::fake();

    $page = DocuWare::documents($this->cabinet)
        ->search()
        ->fileCabinets([$this->cabinet])
        ->fullText(null)
        ->orderBy('DWSTOREDATETIME')
        ->get();

    expect($page)->toBeInstanceOf(DocumentPageData::class);

    Event::assertDispatched(ResponseReceived::class);
});

it('can search documents with multiple values', function () {
    Event::fake();

    $textField = sandboxFieldName($this->cabinet, 'Text');

    // Three documents with distinct values in the discovered text field; the search
    // below should match exactly the first two.
    uploadTestDocument($this->cabinet, 'Abrechnung');
    uploadTestDocument($this->cabinet, 'Rechnung');
    uploadTestDocument($this->cabinet, 'EtwasAnderes');

    Sleep::for(3)->seconds(); // Wait for the documents to be indexed.

    $page = DocuWare::documents($this->cabinet)
        ->search()
        ->fileCabinets([$this->cabinet])
        ->whereIn($textField, ['Abrechnung', 'Rechnung'])
        ->get();

    expect($page)->toBeInstanceOf(DocumentPageData::class)
        ->and($page->documents)->toHaveCount(2);

    Event::assertDispatched(ResponseReceived::class);
});
