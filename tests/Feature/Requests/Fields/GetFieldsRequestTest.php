<?php

use CodebarAg\DocuWare\Connectors\DocuWareStaticConnector;
use CodebarAg\DocuWare\DTO\Config;
use CodebarAg\DocuWare\DTO\Field;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Fields\GetFieldsRequest;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
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

it('can list fields for a file cabinet', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $fields = $this->connector->send(new GetFieldsRequest($fileCabinetId))->dto();

    $this->assertInstanceOf(Collection::class, $fields);

    foreach ($fields as $field) {
        $this->assertInstanceOf(Field::class, $field);
    }

    $this->assertNotCount(0, $fields);

    Event::assertDispatched(DocuWareResponseLog::class);
});
