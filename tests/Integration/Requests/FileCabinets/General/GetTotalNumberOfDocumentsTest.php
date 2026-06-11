<?php

use CodebarAg\DocuWare\Requests\FileCabinets\General\GetTotalNumberOfDocuments;

it('can get a total count of documents', function () {
    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = sandboxSearchDialogId($this->connector);

    $before = $this->connector->send(new GetTotalNumberOfDocuments($fileCabinetId, $dialogId))->dto();

    expect($before)->toBeInt();

    uploadTestDocument($this->connector);

    $after = $this->connector->send(new GetTotalNumberOfDocuments($fileCabinetId, $dialogId))->dto();

    expect($after)->toBe($before + 1);
})->group('live');
