<?php

use CodebarAg\DocuWare\DTO\FileCabinets\Dialog;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\Dialogs\GetASpecificDialog;
use Illuminate\Support\Facades\Event;

it('can get a dialog', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

    $dialog = $this->connector->send(new GetASpecificDialog($fileCabinetId, $dialogId))->dto();

    $this->assertInstanceOf(Dialog::class, $dialog);

    Event::assertDispatched(DocuWareResponseLog::class);
});
