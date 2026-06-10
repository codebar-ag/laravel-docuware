<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\AddApplicationProperties;
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\UpdateApplicationProperties;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('updates application properties on a document', function () {
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
        [
            ['Name' => 'Key1', 'Value' => 'original'],
            ['Name' => 'Key2', 'Value' => 'keep'],
        ],
    ))->dto();

    $updated = $this->connector->send(new UpdateApplicationProperties(
        $fileCabinetId,
        $document->id,
        [['Name' => 'Key1', 'Value' => 'updated']],
    ))->dto()->sortBy('Name');

    expect($updated)->toBeInstanceOf(Collection::class)
        ->and($updated->firstWhere('Name', 'Key1')['Value'])->toBe('updated');

    Event::assertDispatched(DocuWareResponseLog::class);
});
