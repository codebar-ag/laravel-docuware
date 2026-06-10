<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\AddApplicationProperties;
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\DeleteApplicationProperties;
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\GetApplicationProperties;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('deletes application properties from a document', function () {
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
            ['Name' => 'Key1', 'Value' => 'v1'],
            ['Name' => 'Key2', 'Value' => 'v2'],
        ],
    ))->dto();

    $afterDelete = $this->connector->send(new DeleteApplicationProperties(
        $fileCabinetId,
        $document->id,
        ['Key1'],
    ))->dto();

    expect($afterDelete)->toBeInstanceOf(Collection::class)
        ->and($afterDelete->count())->toBe(1)
        ->and($afterDelete->first()['Name'])->toBe('Key2');

    $final = $this->connector->send(new GetApplicationProperties(
        $fileCabinetId,
        $document->id,
    ))->dto();

    expect($final->count())->toBe(1);

    Event::assertDispatched(DocuWareResponseLog::class);
});
