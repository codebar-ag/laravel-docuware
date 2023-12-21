<?php

use CodebarAg\DocuWare\DTO\Organization;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use CodebarAg\DocuWare\Requests\History\GetWorkflowDocumentHistoryRequest;
use CodebarAg\DocuWare\Requests\Organization\GetOrganizationRequest;
use Illuminate\Support\Facades\Event;

it('can get an organization', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $history = $this->connector->send(new GetWorkflowDocumentHistoryRequest($fileCabinetId, $document->id))->dto();

    ray($history);

//    $this->assertInstanceOf(Organization::class, $organization);
    Event::assertDispatched(DocuWareResponseLog::class);
})->only();
