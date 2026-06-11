<?php

use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

it('adds application properties to a document', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);

    $added = DocuWare::documents($this->cabinet)->addApplicationProperties((string) $document->id, [
        ['Name' => 'Key1', 'Value' => 'Key1 Value'],
        ['Name' => 'Key2', 'Value' => 'Key2 Value'],
    ]);

    $properties = collect(Arr::get($added, 'Property', []));

    expect($properties->count())->toBe(2)
        ->and($properties->first()['Name'])->toBe('Key1');

    Event::assertDispatched(ResponseReceived::class);
});
