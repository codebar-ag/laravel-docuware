<?php

use CodebarAg\DocuWare\Data\Write\IndexFields;
use CodebarAg\DocuWare\Facades\DocuWare;
use Illuminate\Support\Facades\Event;

it('can create encrypted url for a document in a file cabinet', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $document = uploadTestDocument($fileCabinetId);

    refreshDocumentAfterProcessing($fileCabinetId, $document->id);

    $url = DocuWare::url(
        url: config('laravel-docuware.credentials.url'),
        username: config('laravel-docuware.credentials.username'),
        password: config('laravel-docuware.credentials.password'),
        passphrase: config('laravel-docuware.passphrase'),
    )
        ->fileCabinet($fileCabinetId)
        ->document($document->id)
        ->validUntil(now()->addMinute())
        ->make();

    $platform = trim(config('laravel-docuware.platform_path', 'DocuWare/Platform'), '/');
    $endpoint = sprintf(
        '%s/%s/WebClient/Integration?ep=',
        rtrim((string) config('laravel-docuware.credentials.url'), '/'),
        $platform,
    );

    expect($url)->toStartWith($endpoint);
})->group('integration');

it('can create encrypted url for a document in a basket', function () {
    Event::fake();

    $basketId = config('laravel-docuware.tests.basket_id');

    $document = DocuWare::documents($basketId)->store(
        fileContent: '::fake-file-content::',
        fileName: 'example.txt',
        indexes: IndexFields::make(),
    );

    refreshDocumentAfterProcessing($basketId, $document->id);

    $url = DocuWare::url(
        url: config('laravel-docuware.credentials.url'),
        username: config('laravel-docuware.credentials.username'),
        password: config('laravel-docuware.credentials.password'),
        passphrase: config('laravel-docuware.passphrase'),
    )
        ->basket($basketId)
        ->document($document->id)
        ->validUntil(now()->addMinute())
        ->make();

    $platform = trim(config('laravel-docuware.platform_path', 'DocuWare/Platform'), '/');
    $endpoint = sprintf(
        '%s/%s/WebClient/Integration?ep=',
        rtrim((string) config('laravel-docuware.credentials.url'), '/'),
        $platform,
    );

    expect($url)->toStartWith($endpoint);
})->group('integration');
