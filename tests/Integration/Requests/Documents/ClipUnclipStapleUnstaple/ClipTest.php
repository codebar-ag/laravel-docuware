<?php

use CodebarAg\DocuWare\Data\Documents\DocumentData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;

it('can clip 2 documents', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);
    $document2 = uploadTestDocument($this->cabinet);

    $clip = DocuWare::documents($this->cabinet)->clip([
        $document->id,
        $document2->id,
    ]);

    expect($clip)->toBeInstanceOf(DocumentData::class)
        ->and($clip->id)->toBe($document->id)
        ->and($clip->total_pages)->toBe($document->total_pages + $document2->total_pages);

    Event::assertDispatched(ResponseReceived::class);
})->group('clip');
