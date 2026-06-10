<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\SelectLists\GetFilteredSelectLists;
use CodebarAg\DocuWare\Requests\FileCabinets\SelectLists\GetSelectLists;
use Illuminate\Support\Facades\Event;

it('returns a select list for a dialog field', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');
    $fieldName = config('laravel-docuware.tests.filtered_select_list_field');

    $response = $this->connector->send(new GetSelectLists(
        $fileCabinetId,
        $dialogId,
        $fieldName,
    ));

    expect($response->successful())->toBeTrue('HTTP '.$response->status().': '.$response->body());

    $values = $response->dto();
    expect($values !== null)->toBeTrue();

    Event::assertDispatched(DocuWareResponseLog::class);
});

it('returns a filtered select list using DialogExpression', function () {
    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');
    $fieldName = config('laravel-docuware.tests.filtered_select_list_field');
    $conditionField = config('laravel-docuware.tests.filtered_select_list_condition_field');
    $conditionValue = config('laravel-docuware.tests.filtered_select_list_condition_value');

    $dialogExpression = [
        'Operation' => 'And',
        'Condition' => [
            [
                'DBName' => $conditionField,
                'Value' => [$conditionValue],
            ],
        ],
    ];

    $response = $this->connector->send(new GetFilteredSelectLists(
        $fileCabinetId,
        $dialogId,
        $fieldName,
        $dialogExpression,
    ));

    expect($response->successful())->toBeTrue('HTTP '.$response->status().': '.$response->body());

    /** @var mixed $payload */
    $payload = $response->json();
    expect(is_array($payload))->toBeTrue();
});
