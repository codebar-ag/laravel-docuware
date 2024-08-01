<?php

namespace CodebarAg\DocuWare\Tests\Feature;

use CodebarAg\DocuWare\DocuWare;
use Illuminate\Support\Facades\Event;

it('can create encrypted url for a document in a file cabinet', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $documentId = config('laravel-docuware.tests.document_id');

    $url = (new DocuWare)
        ->url()
        ->fileCabinet($fileCabinetId)
        ->document($documentId)
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
    $documentId = config('laravel-docuware.tests.document_id');

    $url = (new DocuWare)
        ->url(
            url: config('laravel-docuware.credentials.url'),
            username: config('laravel-docuware.credentials.username'),
            password: config('laravel-docuware.credentials.password'),
            passphrase: config('laravel-docuware.credentials.passphrase'),
        )
        ->basket($basketId)
        ->document($documentId)
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
