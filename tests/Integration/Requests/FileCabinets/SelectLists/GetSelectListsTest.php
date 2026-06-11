<?php

use CodebarAg\DocuWare\Requests\FileCabinets\SelectLists\GetSelectLists;

it('can list values for a select list of a discovered field', function () {
    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = sandboxSearchDialogId($this->connector);
    $keywordField = sandboxFieldName($this->connector, 'Keyword');

    // Seed a value so the dynamic select list has something to return.
    uploadTestDocument($this->connector);

    $response = recordFixture(
        new GetSelectLists($fileCabinetId, $dialogId, $keywordField),
        'file-cabinets/select-lists/get-select-lists',
    );

    expect($response->successful())->toBeTrue('HTTP '.$response->status().': '.$response->body());
    expect($response->dto())->toBeArray();
})->group('live');
