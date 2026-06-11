<?php

use CodebarAg\DocuWare\Data\Organization\FileCabinetData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('lists file cabinets and document trays for the organization', function () {
    Event::fake();

    $items = DocuWare::fileCabinets()->all();

    expect($items)->toBeInstanceOf(Collection::class)
        ->and($items)->not->toBeEmpty()
        ->and($items->first())->toBeInstanceOf(FileCabinetData::class);

    Event::assertDispatched(ResponseReceived::class);
})->group('integration');
