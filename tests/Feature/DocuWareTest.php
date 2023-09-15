<?php

namespace CodebarAg\DocuWare\Tests\Feature;

use CodebarAg\DocuWare\Connectors\DocuWareWithoutCookieConnector;
use CodebarAg\DocuWare\DocuWare;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

beforeEach(function () {
    EnsureValidCookie::check();

    $this->connector = new DocuWareWithoutCookieConnector(config('docuware.cookies'));
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
