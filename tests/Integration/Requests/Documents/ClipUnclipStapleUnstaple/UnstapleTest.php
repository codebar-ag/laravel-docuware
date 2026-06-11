<?php

use CodebarAg\DocuWare\Data\Documents\DocumentData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('can unstaple a document', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);
    $document2 = uploadTestDocument($this->cabinet);

    $staple = DocuWare::documents($this->cabinet)->staple([
        $document->id,
        $document2->id,
    ]);

    Sleep::for(5)->seconds();

    $unstaple = DocuWare::documents($this->cabinet)->unstaple((string) $staple->id);

    expect($unstaple)->toBeInstanceOf(DocumentData::class);

    Event::assertDispatched(ResponseReceived::class);
})->group('unstaple');
