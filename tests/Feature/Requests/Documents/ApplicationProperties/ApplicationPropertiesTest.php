<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\AddApplicationProperties;
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\DeleteApplicationProperties;
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\GetApplicationProperties;
use CodebarAg\DocuWare\Requests\Documents\ApplicationProperties\UpdateApplicationProperties;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can add get update delete application properties to a document', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $addProperties = $this->connector->send(new AddApplicationProperties(
        $fileCabinetId,
        $document->id,
        [
            [
                'Name' => 'Key1',
                'Value' => 'Key1 Value',
            ],
            [
                'Name' => 'Key2',
                'Value' => 'Key2 Value',
            ],
        ],
    ))->dto();

    expect($addProperties)->toBeInstanceOf(Collection::class)
        ->and($addProperties->count())->toBe(2)
        ->and($addProperties->first()['Name'])->toBe('Key1')
        ->and($addProperties->first()['Value'])->toBe('Key1 Value')
        ->and($addProperties->last()['Name'])->toBe('Key2')
        ->and($addProperties->last()['Value'])->toBe('Key2 Value');

    $updateProperties = $this->connector->send(new UpdateApplicationProperties(
        $fileCabinetId,
        $document->id,
        [
            [
                'Name' => 'Key1',
                'Value' => 'Key1 Value Updated',
            ],
        ],
    ))->dto()->sortBy('Name');

    expect($updateProperties)->toBeInstanceOf(Collection::class)
        ->and($updateProperties->count())->toBe(2)
        ->and($updateProperties->first()['Name'])->toBe('Key1')
        ->and($updateProperties->first()['Value'])->toBe('Key1 Value Updated')
        ->and($updateProperties->last()['Name'])->toBe('Key2')
        ->and($updateProperties->last()['Value'])->toBe('Key2 Value');

    $deleteProperties = $this->connector->send(new DeleteApplicationProperties(
        $fileCabinetId,
        $document->id,
        [
            'Key1',
        ],
    ))->dto();

    expect($deleteProperties)->toBeInstanceOf(Collection::class)
        ->and($deleteProperties->count())->toBe(1)
        ->and($deleteProperties->first()['Name'])->toBe('Key2')
        ->and($deleteProperties->first()['Value'])->toBe('Key2 Value');

    $properties = $this->connector->send(new GetApplicationProperties(
        $fileCabinetId,
        $document->id,
    ))->dto();

    expect($properties)->toBeInstanceOf(Collection::class)
        ->and($properties->count())->toBe(1)
        ->and($properties->first()['Name'])->toBe('Key2')
        ->and($properties->first()['Value'])->toBe('Key2 Value');

    Event::assertDispatched(DocuWareResponseLog::class);
})->only();
