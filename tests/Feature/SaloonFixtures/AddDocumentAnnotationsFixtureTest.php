<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\Stamps\AddDocumentAnnotations;
use CodebarAg\DocuWare\Tests\Support\FixtureDocuWareConnector;
use Illuminate\Support\Facades\Event;
use Saloon\Http\Faking\Fixture;
use Saloon\Http\Faking\MockClient;

it('maps AddDocumentAnnotations through a Saloon fixture file', function () {
    Event::fake();

    $mockClient = new MockClient([
        AddDocumentAnnotations::class => new Fixture('add-document-annotations'),
    ]);

    $connector = (new FixtureDocuWareConnector)->withMockClient($mockClient);

    $dto = $connector->send(new AddDocumentAnnotations(
        'cabinet-fixture',
        1,
        ['Annotations' => []],
    ))->dto();

    expect($dto)->toBeArray()
        ->and($dto['Ok'] ?? null)->toBeTrue();

    Event::assertDispatched(DocuWareResponseLog::class);
});
