<?php

use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

it('updates application properties on a document', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);

    DocuWare::documents($this->cabinet)->addApplicationProperties((string) $document->id, [
        ['Name' => 'Key1', 'Value' => 'original'],
        ['Name' => 'Key2', 'Value' => 'keep'],
    ]);

    $updated = collect(Arr::get(
        DocuWare::documents($this->cabinet)->updateApplicationProperties((string) $document->id, [
            ['Name' => 'Key1', 'Value' => 'updated'],
        ]),
        'Property',
        [],
    ))->sortBy('Name');

    expect($updated->firstWhere('Name', 'Key1')['Value'])->toBe('updated');

    Event::assertDispatched(ResponseReceived::class);
});
