<?php

use CodebarAg\DocuWare\Connectors\DocuWareStaticConnector;
use CodebarAg\DocuWare\DTO\Config;
use CodebarAg\DocuWare\DTO\Document;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Document\DeleteDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentsRequest;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
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

it('can get all documents', function () {
    Event::fake();

    $this->connector->send(new PostDocumentRequest(
        config('laravel-docuware.tests.file_cabinet_id'),
        '::fake-file-content::',
        'example.txt'
    ))->dto();
    $this->connector->send(new PostDocumentRequest(
        config('laravel-docuware.tests.file_cabinet_id'),
        '::fake-file-content::',
        'example.txt'
    ))->dto();

    $documents = $this->connector->send(new GetDocumentsRequest(
        config('laravel-docuware.tests.file_cabinet_id')
    ))->dto();

    foreach ($documents as $document) {
        $this->assertInstanceOf(Document::class, $document);

        $this->connector->send(new DeleteDocumentRequest(
            config('laravel-docuware.tests.file_cabinet_id'),
            $document->id,
        ))->dto();
    }

    Event::assertDispatched(DocuWareResponseLog::class);
});
