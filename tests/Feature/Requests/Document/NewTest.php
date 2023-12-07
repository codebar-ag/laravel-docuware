<?php

use CodebarAg\DocuWare\Connectors\DocuWareStaticConnector;
use CodebarAg\DocuWare\DTO\Config;
use CodebarAg\DocuWare\DTO\DocumentIndex\IndexTableDTO;
use CodebarAg\DocuWare\DTO\DocumentIndex\PrepareDTO;
use CodebarAg\DocuWare\DTO\TableRow;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Document\DeleteDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\GetDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\PostDocumentRequest;
use CodebarAg\DocuWare\Requests\Document\PutDocumentFieldsRequest;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
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

it('can update a document value', function () {
    Event::fake();

    $fileCabinetId = '9500dab8-2e8c-4f83-944f-6089dbfe15ba';
    $documentId = 6;

    $document = $this->connector->send(new GetDocumentRequest(
        $fileCabinetId,
        $documentId
    ))->dto();

    $items = $document->fields->where('name', 'ITEMS')->first();

    $updatedItems = $items->value->map(function (TableRow $item)  {
        return $item->fields->map(function ($column, $key) {
            $value = $column->value;

            return [
                'NAME' => $key,
                'VALUE' => $value,
            ];
        });
    })->values();

    ray($items);

    $instance = IndexTableDTO::make('ITEMS', $updatedItems);

    ray($instance);

    $response = $this->connector->send(new PutDocumentFieldsRequest(
        $fileCabinetId,
        $documentId,
        collect([$instance])
    ))->dto();

    ray($response);
})->only();
