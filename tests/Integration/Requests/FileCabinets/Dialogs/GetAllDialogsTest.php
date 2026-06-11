<?php

use CodebarAg\DocuWare\Data\FileCabinets\DialogData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can list dialogs for a file cabinet', function () {
    Event::fake();

    $dialogs = DocuWare::dialogs($this->cabinet)->all();

    expect($dialogs)->toBeInstanceOf(Collection::class)
        ->and($dialogs)->not->toBeEmpty();

    foreach ($dialogs as $dialog) {
        expect($dialog)->toBeInstanceOf(DialogData::class);
    }

    Event::assertDispatched(ResponseReceived::class);
});
