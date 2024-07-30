<?php

use CodebarAg\DocuWare\DTO\FileCabinets\Dialog;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\Dialogs\GetASpecificDialog;
use Illuminate\Support\Facades\Event;

it('can get a dialog', function () {
    Event::fake();

    $fileCabinetId = env('DOCUWARE_TESTS_FILE_CABINET_ID');
    $dialogId = env('DOCUWARE_TESTS_DIALOG_ID');

    $dialog = $this->connector->send(new GetASpecificDialog($fileCabinetId, $dialogId))->dto();

    $this->assertInstanceOf(Dialog::class, $dialog);

    Event::assertDispatched(DocuWareResponseLog::class);
});
