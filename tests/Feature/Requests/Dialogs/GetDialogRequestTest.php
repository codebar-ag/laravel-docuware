<?php

use CodebarAg\DocuWare\DTO\Dialog;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Dialogs\GetDialogRequest;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

beforeEach(function () {
    $this->connector = getConnector();
});

it('can get a dialog', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

    $dialog = $this->connector->send(new GetDialogRequest($fileCabinetId, $dialogId))->dto();

    $this->assertInstanceOf(Dialog::class, $dialog);

    Event::assertDispatched(DocuWareResponseLog::class);
});
