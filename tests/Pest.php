<?php

use CodebarAg\DocuWare\Connectors\DocuWareStaticConnector;
use CodebarAg\DocuWare\DTO\Config;
use CodebarAg\DocuWare\Requests\Document\DeleteDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentsRequest;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use CodebarAg\DocuWare\Tests\TestCase;

uses(TestCase::class)
    ->group('docuware')
    ->in(__DIR__);

uses()
    ->beforeEach(function () {
        $this->connector = getConnector();

//        clearFiles();
    })
    ->in('Feature');

/**
 * @throws Throwable
 */
function getConnector(): object
{
    EnsureValidCookie::check();

    $config = Config::make([
        'url' => config('laravel-docuware.credentials.url'),
        'cookie' => config('laravel-docuware.cookies'),
        'cache_driver' => config('laravel-docuware.configurations.cache.driver'),
        'cache_lifetime_in_seconds' => config('laravel-docuware.configurations.cache.lifetime_in_seconds'),
        'request_timeout_in_seconds' => config('laravel-docuware.timeout'),
    ]);

    return new DocuWareStaticConnector($config);
}

function clearFiles(): void
{
    $connector = getConnector();

    $documents = $connector->send(new GetDocumentsRequest(
        config('laravel-docuware.tests.file_cabinet_id')
    ))->dto();

    foreach ($documents as $document) {
        $delete = $connector->send(new DeleteDocumentRequest(
            config('laravel-docuware.tests.file_cabinet_id'),
            $document->id,
        ))->dto();
    }
}
