<?php

use CodebarAg\DocuWare\DTO\FileCabinet;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\GetFileCabinetsRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

beforeEach(function () {
    $this->connector = getConnector();
});

it('can list file cabinets', function () {
    Event::fake();

    $fileCabinets = $this->connector->send(new GetFileCabinetsRequest())->dto();

    $this->assertInstanceOf(Collection::class, $fileCabinets);

    foreach ($fileCabinets as $fileCabinet) {
        $this->assertInstanceOf(FileCabinet::class, $fileCabinet);
    }

    $this->assertNotCount(0, $fileCabinets);
    Event::assertDispatched(DocuWareResponseLog::class);

});
