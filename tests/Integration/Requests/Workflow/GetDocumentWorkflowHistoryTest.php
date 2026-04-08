<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use CodebarAg\DocuWare\Requests\Workflow\GetDocumentWorkflowHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can get document workflow history', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $document = refreshDocumentAfterProcessing($this->connector, $fileCabinetId, $document->id);

    $history = $this->connector->send(new GetDocumentWorkflowHistory(
        $fileCabinetId,
        $document->id
    ))->dto();

    expect($history)->toBeInstanceOf(Collection::class);

    if ($history->isEmpty()) {
        Event::assertDispatched(DocuWareResponseLog::class);

        return;
    }

    expect($history->first())->toHaveKeys([
        'id',
        'workflowId',
        'name',
        'version',
        'workflowRequest',
        'startedAt',
        'docId',
    ]);

    Event::assertDispatched(DocuWareResponseLog::class);
})->group('workflow');
