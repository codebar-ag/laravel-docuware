<?php

use CodebarAg\DocuWare\DTO\FileCabinets\General\FileCabinetInformation;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\General\GetFileCabinetInformation;
use Illuminate\Support\Facades\Event;

it('can get file cabinet information', function () {
    Event::fake();

    $fileCabinet = $this->connector->send(new GetFileCabinetInformation(env('DOCUWARE_TESTS_FILE_CABINET_ID')))->dto();

    $this->assertInstanceOf(FileCabinetInformation::class, $fileCabinet);

    Event::assertDispatched(DocuWareResponseLog::class);
});
