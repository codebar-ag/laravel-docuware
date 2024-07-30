<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use CodebarAg\DocuWare\Requests\Workflow\GetDocumentWorkflowHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('can get document workflow history', function () {
    Event::fake();

    $document = $this->connector->send(new CreateDataRecord(
        env('DOCUWARE_TESTS_FILE_CABINET_ID'),
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    Sleep::for(5)->seconds();

    $history = $this->connector->send(new GetDocumentWorkflowHistory(
        env('DOCUWARE_TESTS_FILE_CABINET_ID'),
        $document->id
    ))->dto();

    expect($history)->toBeInstanceOf(Collection::class)
        ->and($history->first())->toHaveKeys([
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
