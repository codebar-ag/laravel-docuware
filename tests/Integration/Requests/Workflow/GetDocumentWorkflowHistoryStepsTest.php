<?php

use CodebarAg\DocuWare\Data\Workflow\InstanceHistoryData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can get document workflow history steps', function () {
    Event::fake();

    $document = uploadTestDocument($this->cabinet);

    $document = refreshDocumentAfterProcessing($this->cabinet, $document->id);

    $history = DocuWare::documents($this->cabinet)->workflowHistory((string) $document->id);

    expect($history)->toBeInstanceOf(Collection::class);

    if ($history->isEmpty()) {
        Event::assertDispatched(ResponseReceived::class);

        return;
    }

    $instance = $history->first();

    $historySteps = DocuWare::workflows()->historySteps(
        $instance->workflowId,
        $instance->id,
    );

    expect($historySteps)->toBeInstanceOf(InstanceHistoryData::class)
        ->and($historySteps->historySteps)->toBeInstanceOf(Collection::class);

    Event::assertDispatched(ResponseReceived::class);
})->group('integration', 'workflow');
