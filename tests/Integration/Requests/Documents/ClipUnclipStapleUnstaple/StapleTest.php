<?php

use CodebarAg\DocuWare\Data\Documents\DocumentData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;

it('can staple 2 documents', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);
    $document2 = uploadTestDocument($this->cabinet);

    $staple = DocuWare::documents($this->cabinet)->staple([
        $document->id,
        $document2->id,
    ]);

    $expectedMinPages = $document->total_pages + $document2->total_pages;

    expect($staple)->toBeInstanceOf(DocumentData::class)
        ->and($staple->title)->toBe($document->title)
        ->and($staple->total_pages)->toBeGreaterThanOrEqual($expectedMinPages);

    Event::assertDispatched(ResponseReceived::class);
})->group('staple');
