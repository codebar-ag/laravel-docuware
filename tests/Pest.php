<?php

use CodebarAg\DocuWare\Connectors\DocuWareStaticConnector;
use CodebarAg\DocuWare\DTO\Config;
use CodebarAg\DocuWare\Requests\Document\DeleteDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentsRequest;
use CodebarAg\DocuWare\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

beforeAll(function () {
    $config = Config::make([
        'url' => config('docuware.credentials.url'),
        'cookie' => config('docuware.cookies'),
        'cache_driver' => config('docuware.configurations.cache.driver'),
        'cache_lifetime_in_seconds' => config('docuware.configurations.cache.lifetime_in_seconds'),
        'request_timeout_in_seconds' => config('docuware.timeout'),
    ]);

    $connector = new DocuWareStaticConnector($config);

    $documents = $connector->send(new GetDocumentsRequest(
        config('docuware.tests.file_cabinet_id')
    ))->dto();

    foreach ($documents as $document) {
        $connector->send(new DeleteDocumentRequest(
            config('docuware.tests.file_cabinet_id'),
            $document->id,
        ))->dto();
    }
});
