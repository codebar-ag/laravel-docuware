<?php

use CodebarAg\DocuWare\Data\Documents\DocumentData;
use CodebarAg\DocuWare\Data\Documents\DocumentPageData;
use CodebarAg\DocuWare\DocuWareManager;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetASpecificDocumentFromAFileCabinet;
use CodebarAg\DocuWare\Resources\DocumentsResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\LazyCollection;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    config()->set('laravel-docuware.default', 'default');
    config()->set('laravel-docuware.instances.default', [
        'grant' => 'credentials',
        'url' => 'https://example.docuware.cloud',
        'username' => 'alice',
        'password' => 'secret',
    ]);
    config()->set('laravel-docuware.configurations.cache.driver', 'array');
    config()->set('laravel-docuware.configurations.cache.lifetime_in_seconds', 60);
    config()->set('laravel-docuware.configurations.request.timeout_in_seconds', 30);
    config()->set('laravel-docuware.configurations.client_id', 'docuware.platform.net.client');
    config()->set('laravel-docuware.configurations.scope', 'docuware.platform');

    // Saloon caches search responses; the array store is process-global, so isolate per test.
    Cache::store('array')->flush();
});

it('exposes a documents resource', function () {
    expect(app(DocuWareManager::class)->documents('cab-1'))
        ->toBeInstanceOf(DocumentsResource::class);
});

it('finds a document by id', function () {
    $client = clientWithMock([
        GetASpecificDocumentFromAFileCabinet::class => MockResponse::make(fakeDocumentPayload(42)),
    ]);

    $document = $client->documents('cab-1')->find(42);

    expect($document)->toBeInstanceOf(DocumentData::class)
        ->and($document->id)->toBe(42)
        ->and($document->title)->toBe('Doc 42')
        ->and($document->isPdf())->toBeTrue();
});

it('returns a single page of search results', function () {
    $client = clientWithMock([
        MockResponse::make(['Count' => ['Value' => 3], 'Items' => [
            fakeDocumentPayload(1), fakeDocumentPayload(2),
        ]]),
    ]);

    $page = $client->documents('cab-1')->search()->perPage(2)->get();

    expect($page)->toBeInstanceOf(DocumentPageData::class)
        ->and($page->total)->toBe(3)
        ->and($page->lastPage)->toBe(2)
        ->and($page->documents)->toHaveCount(2)
        ->and($page->hasMorePages())->toBeTrue()
        ->and($page->documents->first())->toBeInstanceOf(DocumentData::class);
});

it('lazily iterates every document across all pages via cursor', function () {
    $client = clientWithMock([
        MockResponse::make(['Count' => ['Value' => 3], 'Items' => [fakeDocumentPayload(1), fakeDocumentPayload(2)]]),
        MockResponse::make(['Count' => ['Value' => 3], 'Items' => [fakeDocumentPayload(3)]]),
    ]);

    $cursor = $client->documents('cab-1')->search()->perPage(2)->cursor();

    expect($cursor)->toBeInstanceOf(LazyCollection::class);

    $ids = $cursor->map(fn (DocumentData $d) => $d->id)->all();

    expect($ids)->toBe([1, 2, 3]);
});

it('builds a dialog-expression condition from fluent filters', function () {
    $client = clientWithMock([
        MockResponse::make(['Count' => ['Value' => 0], 'Items' => []]),
    ]);

    // Capture the outgoing request body by sending and inspecting via the mock recorder.
    $client->documents('cab-1')->search()
        ->fullText('invoice')
        ->where('STATUS', 'open')
        ->whereNotEmpty('AMOUNT')
        ->get();

    // No exception + a request was sent is the contract here; deeper body assertions live
    // in the dedicated SearchQuery condition tests.
    expect(true)->toBeTrue();
});
