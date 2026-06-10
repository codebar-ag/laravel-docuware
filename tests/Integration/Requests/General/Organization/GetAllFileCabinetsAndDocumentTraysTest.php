<?php

use CodebarAg\DocuWare\DTO\General\Organization\FileCabinet;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\General\Organization\GetAllFileCabinetsAndDocumentTrays;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('lists file cabinets and document trays for the organization', function () {
    Event::fake();

    $orgId = config('laravel-docuware.tests.org_id');

    $items = $this->connector->send(new GetAllFileCabinetsAndDocumentTrays(
        organizationId: $orgId ? (string) $orgId : null,
    ))->dto();

    expect($items)->toBeInstanceOf(Collection::class)
        ->and($items)->not->toBeEmpty();

    expect($items->first())->toBeInstanceOf(FileCabinet::class);

    Event::assertDispatched(DocuWareResponseLog::class);
});
