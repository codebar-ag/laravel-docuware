<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\SelectLists\GetFilteredSelectLists;
use Illuminate\Support\Facades\Event;

it('returns a filtered select list using DialogExpression', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $dialogId = config('laravel-docuware.tests.dialog_id');

    $dialogExpression = [
        'Operation' => 'And',
        'Condition' => [
            [
                'DBName' => 'DOCUMENT_TYPE',
                'Value' => ['"DocuWare"'],
            ],
        ],
    ];

    $values = $this->connector->send(new GetFilteredSelectLists(
        $fileCabinetId,
        $dialogId,
        'DOCUMENT_TYPE',
        $dialogExpression,
    ))->dto();

    expect($values !== null)->toBeTrue();

    Event::assertDispatched(DocuWareResponseLog::class);
})->skip('Filtered select lists depend on cabinet field names and DialogExpression shape for your tenant.');
