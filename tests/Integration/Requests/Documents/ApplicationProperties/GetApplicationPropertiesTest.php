<?php

use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

it('gets application properties for a document', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);

    DocuWare::documents($this->cabinet)->addApplicationProperties((string) $document->id, [
        ['Name' => 'Key1', 'Value' => 'v'],
    ]);

    $properties = collect(Arr::get(
        DocuWare::documents($this->cabinet)->applicationProperties((string) $document->id),
        'Property',
        [],
    ));

    expect($properties->count())->toBeGreaterThanOrEqual(1);

    Event::assertDispatched(ResponseReceived::class);
});
