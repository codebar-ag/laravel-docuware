<?php

use CodebarAg\DocuWare\Connectors\DocuWareStaticConnector;
use CodebarAg\DocuWare\DTO\Config;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Document\DeleteDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\PutDocumentFieldsRequest;
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

it('can update a document value', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $newValue = 'laravel-docuware';

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $response = $this->connector->send(new PutDocumentFieldsRequest(
        $fileCabinetId,
        $document->id,
        ['UUID' => $newValue]
    ))->dto();

    $this->assertSame('laravel-docuware', $response['UUID']);
    Event::assertDispatched(DocuWareResponseLog::class);

    $this->connector->send(new DeleteDocumentRequest(
        $fileCabinetId,
        $document->id
    ))->dto();
});

it('can update multiple document values', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $values = [
        'UUID' => 'laravel-docuware',
        'DOCUMENT_LABEL' => 'laravel-docuware-2',
    ];

    $document = $this->connector->send(new PostDocumentRequest(
        $fileCabinetId,
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $response = $this->connector->send(new PutDocumentFieldsRequest(
        $fileCabinetId,
        $document->id,
        $values,
        true
    ))->dto();

    $this->assertSame('laravel-docuware', $response['UUID']);
    $this->assertSame('laravel-docuware-2', $response['DOCUMENT_LABEL']);

    Event::assertDispatched(DocuWareResponseLog::class);

    $this->connector->send(new DeleteDocumentRequest(
        $fileCabinetId,
        $document->id
    ))->dto();
});
