<?php

use CodebarAg\DocuWare\Data\Documents\FieldData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can list fields for a file cabinet', function () {
    Event::fake();

    $fields = DocuWare::fileCabinets()->fields($this->cabinet);

    expect($fields)->toBeInstanceOf(Collection::class)
        ->and($fields)->not->toBeEmpty();

    foreach ($fields as $field) {
        expect($field)->toBeInstanceOf(FieldData::class);
    }

    Event::assertDispatched(ResponseReceived::class);
});
