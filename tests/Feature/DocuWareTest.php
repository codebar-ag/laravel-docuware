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
        'url' => config('docuware.credentials.url'),
        'cookie' => config('docuware.cookies'),
        'cache_driver' => config('docuware.configurations.cache.driver'),
        'cache_lifetime_in_seconds' => config('docuware.configurations.cache.lifetime_in_seconds'),
        'request_timeout_in_seconds' => config('docuware.timeout'),
    ]);

    $this->connector = new DocuWareStaticConnector($config);
});

it('can create encrypted url for a document in a file cabinet', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $documentId = config('docuware.tests.document_id');

    $url = (new DocuWare())
        ->url()
        ->fileCabinet($fileCabinetId)
        ->document($documentId)
        ->validUntil(now()->addMinute())
        ->make();

    $endpoint = sprintf(
        '%s/DocuWare/Platform/WebClient/Integration?ep=',
        config('docuware.credentials.url'),
    );

    $this->assertStringStartsWith(
        $endpoint,
        $url,
    );
});

it('can create encrypted url for a document in a basket', function () {
    Event::fake();

    $basketId = config('docuware.tests.basket_id');
    $documentId = config('docuware.tests.document_id');

    $url = (new DocuWare())
        ->url()
        ->basket($basketId)
        ->document($documentId)
        ->validUntil(now()->addMinute())
        ->make();

    $endpoint = sprintf(
        '%s/DocuWare/Platform/WebClient/Integration?ep=',
        config('docuware.credentials.url'),
    );

    $this->assertStringStartsWith(
        $endpoint,
        $url,
    );
});
