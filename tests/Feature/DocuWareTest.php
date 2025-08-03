<?php

namespace CodebarAg\DocuWare\Tests\Feature;

use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\Requests\FileCabinets\Upload\CreateDataRecord;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;

it('can create encrypted url for a document in a file cabinet', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    // Create a document dynamically for testing
    $document = $this->connector->send(new CreateDataRecord(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    Sleep::for(2)->seconds(); // Wait for the document to be processed

    $url = (new DocuWare)
        ->url(
            url: config('laravel-docuware.credentials.url'),
            username: config('laravel-docuware.credentials.username'),
            password: config('laravel-docuware.credentials.password'),
            passphrase: config('laravel-docuware.passphrase'),
        )
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

    $basketId = config('laravel-docuware.tests.basket_id');

    // Create a document dynamically for testing
    $document = $this->connector->send(new CreateDataRecord(
        $basketId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    Sleep::for(2)->seconds(); // Wait for the document to be processed

    $url = (new DocuWare)
        ->url(
            url: config('laravel-docuware.credentials.url'),
            username: config('laravel-docuware.credentials.username'),
            password: config('laravel-docuware.credentials.password'),
            passphrase: config('laravel-docuware.passphrase'),
        )
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
