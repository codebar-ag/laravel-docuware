<?php

use CodebarAg\DocuWare\DTO\Dialog;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Dialogs\GetDialogsRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

beforeEach(function () {
    $this->connector = getConnector();
});

it('can list dialogs for a file cabinet', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $dialogs = $this->connector->send(new GetDialogsRequest($fileCabinetId))->dto();

    $this->assertInstanceOf(Collection::class, $dialogs);

    $this->assertNotCount(0, $dialogs);

    foreach ($dialogs as $dialog) {
        $this->assertInstanceOf(Dialog::class, $dialog);
    }

    Event::assertDispatched(DocuWareResponseLog::class);
});
