<?php

use CodebarAg\DocuWare\Data\FileCabinets\DialogData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;

it('can get a dialog', function () {
    Event::fake();

    $dialogId = sandboxSearchDialogId($this->cabinet);

    $dialog = DocuWare::dialogs($this->cabinet)->find($dialogId);

    expect($dialog)->toBeInstanceOf(DialogData::class)
        ->and($dialog->id)->toBe($dialogId);

    Event::assertDispatched(ResponseReceived::class);
});
