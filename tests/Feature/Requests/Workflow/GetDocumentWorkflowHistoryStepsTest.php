<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use CodebarAg\DocuWare\Requests\Workflow\GetDocumentWorkflowHistory;
use CodebarAg\DocuWare\Requests\Workflow\GetDocumentWorkflowHistorySteps;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('can get document workflow history', function () {
    Event::fake();

    $document = $this->connector->send(new CreateDataRecord(
        config('laravel-docuware.tests.file_cabinet_id'),
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    Sleep::for(5)->seconds();

    $history = $this->connector->send(new GetDocumentWorkflowHistory(
        config('laravel-docuware.tests.file_cabinet_id'),
        $document->id
    ))->dto();

    $historySteps = $this->connector->send(new GetDocumentWorkflowHistorySteps(
        $history->first()->workflowId,
        $history->first()->id,
    ))->dto();

    expect($historySteps)->toHaveKeys([
        'id',
        'workflowId',
        'name',
        'version',
        'workflowRequest',
        'startedAt',
        'docId',
        'historySteps',
    ])
        ->and($historySteps->historySteps)->toBeInstanceOf(Collection::class);

    Event::assertDispatched(DocuWareResponseLog::class);
})->group('workflow')->skip('Test fails need to check with DocuWare Devs');
