<?php

use CodebarAg\DocuWare\Connectors\DocuWareStaticConnector;
use CodebarAg\DocuWare\DTO\Config;
use CodebarAg\DocuWare\DTO\Dialog;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Dialogs\GetDialogsRequest;
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

it('can list dialogs for a file cabinet', function () {
    Event::fake();

    $fileCabinetId = config('laravel-docuware.tests.file_cabinet_id');

    $dialogs = $this->connector->send(new GetDialogsRequest($fileCabinetId))->dto();

    $this->assertInstanceOf(Collection::class, $dialogs);

    $this->assertNotCount(0, $dialogs);

    foreach ($dialogs as $dialog) {
        $this->assertInstanceOf(Dialog::class, $dialog);
    }

    Event::assertDispatched(DocuWareResponseLog::class);
});
