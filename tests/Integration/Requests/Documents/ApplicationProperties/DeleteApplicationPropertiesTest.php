<?php

use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

it('deletes application properties from a document', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);

    DocuWare::documents($this->cabinet)->addApplicationProperties((string) $document->id, [
        ['Name' => 'Key1', 'Value' => 'v1'],
        ['Name' => 'Key2', 'Value' => 'v2'],
    ]);

    $afterDelete = collect(Arr::get(
        DocuWare::documents($this->cabinet)->deleteApplicationProperties((string) $document->id, ['Key1']),
        'Property',
        [],
    ));

    expect($afterDelete->count())->toBe(1)
        ->and($afterDelete->first()['Name'])->toBe('Key2');

    $final = collect(Arr::get(
        DocuWare::documents($this->cabinet)->applicationProperties((string) $document->id),
        'Property',
        [],
    ));

    expect($final->count())->toBe(1);

    Event::assertDispatched(ResponseReceived::class);
});
