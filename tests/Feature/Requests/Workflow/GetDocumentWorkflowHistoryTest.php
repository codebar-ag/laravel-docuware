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
        config('laravel-docuware.tests.file_cabinet_id'),
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    Sleep::for(5)->seconds();

    $history = $this->connector->debug()->send(new GetDocumentWorkflowHistory(
        config('laravel-docuware.tests.file_cabinet_id'),
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
})->group('workflow')->skip('Test fails need to check with DocuWare Devs');
