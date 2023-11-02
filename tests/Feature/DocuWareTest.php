<?php

namespace CodebarAg\DocuWare\Tests\Feature;

use CodebarAg\DocuWare\Connectors\DocuWareStaticConnector;
use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\DTO\Config;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

beforeEach(function () {
    EnsureValidCookie::check();

    $config = Config::make([
        'url' => config('laravel-docuware.credentials.url'),
        'cookie' => config('laravel-docuware.cookies'),
        'cache_driver' => config('laravel-docuware.configurations.cache.driver'),
        'cache_lifetime_in_seconds' => config('laravel-docuware.configurations.cache.lifetime_in_seconds'),
        'request_timeout_in_seconds' => config('laravel-docuware.timeout'),
    ]);

    $this->connector = new DocuWareStaticConnector($config);
});

it('can create encrypted url for a document in a file cabinet', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');
    $documentId = config('laravel-docuware.tests.document_id');

    $url = (new DocuWare())
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

    $url = (new DocuWare())
        ->url()
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
