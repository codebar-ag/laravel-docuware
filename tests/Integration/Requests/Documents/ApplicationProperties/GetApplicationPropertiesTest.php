<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\AddApplicationProperties;
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\GetApplicationProperties;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('gets application properties for a document', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $this->connector->send(new AddApplicationProperties(
        $fileCabinetId,
        $document->id,
        [['Name' => 'Key1', 'Value' => 'v']],
    ))->dto();

    $properties = $this->connector->send(new GetApplicationProperties(
        $fileCabinetId,
        $document->id,
    ))->dto();

    expect($properties)->toBeInstanceOf(Collection::class)
        ->and($properties->count())->toBeGreaterThanOrEqual(1);

    Event::assertDispatched(DocuWareResponseLog::class);
});
