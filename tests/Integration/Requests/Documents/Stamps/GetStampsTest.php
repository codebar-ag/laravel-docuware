<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\Stamps\GetStamps;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('lists stamp definitions for a file cabinet', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $stamps = $this->connector->send(new GetStamps($fileCabinetId))->dto();

    expect($stamps)->toBeInstanceOf(Collection::class);

    Event::assertDispatched(DocuWareResponseLog::class);
});
