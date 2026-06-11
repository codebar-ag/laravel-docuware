<?php

use CodebarAg\DocuWare\Data\Documents\DocumentData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('can unclip 2 documents', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);
    $document2 = uploadTestDocument($this->cabinet);

    $clip = DocuWare::documents($this->cabinet)->clip([
        $document->id,
        $document2->id,
    ]);

    Sleep::for(5)->seconds();

    $unclip = DocuWare::documents($this->cabinet)->unclip((string) $clip->id);

    expect($unclip)->toBeInstanceOf(DocumentData::class)
        ->and($unclip->title)->toBe($document->title);

    Event::assertDispatched(ResponseReceived::class);
})->group('unclip');
