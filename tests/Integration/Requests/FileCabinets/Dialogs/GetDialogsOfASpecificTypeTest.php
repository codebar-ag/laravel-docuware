<?php

use CodebarAg\DocuWare\Data\FileCabinets\DialogData;
use CodebarAg\DocuWare\Enums\DialogType;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can list dialogs of a specific type for a file cabinet', function () {
    Event::fake();

    $dialogs = DocuWare::dialogs($this->cabinet)->ofType(DialogType::SEARCH);

    expect($dialogs)->toBeInstanceOf(Collection::class)
        ->and($dialogs)->not->toBeEmpty();

    foreach ($dialogs as $dialog) {
        expect($dialog)->toBeInstanceOf(DialogData::class)
            ->and($dialog->type)->toBe(DialogType::SEARCH->value);
    }

    Event::assertDispatched(ResponseReceived::class);
});
