<?php

use CodebarAg\DocuWare\Connectors\DocuWareStaticConnector;
use CodebarAg\DocuWare\DTO\Config;
use CodebarAg\DocuWare\DTO\Dialog;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Dialogs\GetDialogRequest;
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

it('can get a dialog', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');
    $dialogId = config('docuware.tests.dialog_id');

    $dialog = $this->connector->send(new GetDialogRequest($fileCabinetId, $dialogId))->dto();

    $this->assertInstanceOf(Dialog::class, $dialog);

    Event::assertDispatched(DocuWareResponseLog::class);
});
