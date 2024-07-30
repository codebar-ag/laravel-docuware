<?php

namespace CodebarAg\DocuWare\Tests\Feature;

use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;

it('can create encrypted url for a document in a file cabinet', function () {
    Event::fake();

    $fileCabinetId = env('DOCUWARE_TESTS_FILE_CABINET_ID');

    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $url = (new DocuWare)
        ->url()
        ->fileCabinet($fileCabinetId)
        ->document($document->id)
        ->validUntil(now()->addMinute())
        ->make();

    $endpoint = sprintf(
        '%s/DocuWare/Platform/WebClient/Integration?ep=',
        config('laravel-docuware.credentials.url'),
    );

    $this->assertStringStartsWith(
        $endpoint,
        $url,
    );
});

it('can create encrypted url for a document in a basket', function () {
    Event::fake();

    $basketId = env('DOCUWARE_TESTS_BASKET_ID');

    $document = $this->connector->send(new CreateDataRecord(
        $basketId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $url = (new DocuWare)
        ->url()
        ->basket($basketId)
        ->document($document->id)
        ->validUntil(now()->addMinute())
        ->make();

    $endpoint = sprintf(
        '%s/DocuWare/Platform/WebClient/Integration?ep=',
        config('laravel-docuware.credentials.url'),
    );

    $this->assertStringStartsWith(
        $endpoint,
        $url,
    );
});
