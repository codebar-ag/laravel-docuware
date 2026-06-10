<?php

use CodebarAg\DocuWare\DTO\FileCabinets\Dialog;
use CodebarAg\DocuWare\Enums\DialogType;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\Dialogs\GetDialogsOfASpecificType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can list dialogs for a file cabinet', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $dialogs = $this->connector->send(new GetDialogsOfASpecificType($fileCabinetId, DialogType::SEARCH))->dto();

    $this->assertInstanceOf(Collection::class, $dialogs);

    $this->assertNotCount(0, $dialogs);

    foreach ($dialogs as $dialog) {
        $this->assertInstanceOf(Dialog::class, $dialog);
    }

    Event::assertDispatched(DocuWareResponseLog::class);
});
