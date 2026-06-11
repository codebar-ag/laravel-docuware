<?php

use CodebarAg\DocuWare\Requests\FileCabinets\SelectLists\GetFilteredSelectLists;
use CodebarAg\DocuWare\Requests\FileCabinets\SelectLists\GetSelectLists;

it('returns a select list for a discovered dialog field', function () {
    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = sandboxSearchDialogId($this->connector);
    $keywordField = sandboxFieldName($this->connector, 'Keyword');

    $response = $this->connector->send(new GetSelectLists($fileCabinetId, $dialogId, $keywordField));

    expect($response->successful())->toBeTrue('HTTP '.$response->status().': '.$response->body());
    expect($response->dto())->toBeArray();
})->group('live');

it('returns a filtered select list using a DialogExpression', function () {
    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = sandboxSearchDialogId($this->connector);
    $keywordField = sandboxFieldName($this->connector, 'Keyword');
    $textField = sandboxFieldName($this->connector, 'Text');

    $dialogExpression = [
        'Operation' => 'And',
        'Condition' => [
            ['DBName' => $textField, 'Value' => ['value']],
        ],
    ];

    $response = recordFixture(
        new GetFilteredSelectLists($fileCabinetId, $dialogId, $keywordField, $dialogExpression),
        'file-cabinets/select-lists/get-filtered-select-lists',
    );

    expect($response->successful())->toBeTrue('HTTP '.$response->status().': '.$response->body());
    expect($response->json())->toBeArray();
})->group('live');
