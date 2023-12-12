<?php

use CodebarAg\DocuWare\DTO\Field;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Fields\GetFieldsRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

it('can list fields for a file cabinet', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $fields = $this->connector->send(new GetFieldsRequest($fileCabinetId))->dto();

    $this->assertInstanceOf(Collection::class, $fields);

    foreach ($fields as $field) {
        $this->assertInstanceOf(Field::class, $field);
    }

    $this->assertNotCount(0, $fields);

    Event::assertDispatched(DocuWareResponseLog::class);
});
