<?php

use CodebarAg\DocuWare\Data\Documents\DocumentData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('can show a document', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);

    Sleep::for(2)->seconds(); // Wait for the document to be processed

    $getDocument = DocuWare::documents($this->cabinet)->find($document->id);

    expect($getDocument)->toBeInstanceOf(DocumentData::class)
        ->and($getDocument->id)->toBe($document->id)
        ->and($getDocument->file_cabinet_id)->toBe($this->cabinet);

    Event::assertDispatched(ResponseReceived::class);
});
