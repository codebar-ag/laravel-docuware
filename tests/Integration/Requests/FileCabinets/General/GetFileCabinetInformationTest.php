<?php

use CodebarAg\DocuWare\Data\FileCabinets\FileCabinetInformationData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;

it('can get file cabinet information', function () {
    Event::fake();

    $fileCabinet = DocuWare::fileCabinets()->info($this->cabinet);

    expect($fileCabinet)->toBeInstanceOf(FileCabinetInformationData::class)
        ->and($fileCabinet->id)->toBe($this->cabinet);

    Event::assertDispatched(ResponseReceived::class);
});
