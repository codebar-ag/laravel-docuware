<?php

use CodebarAg\DocuWare\Connectors\DocuWareWithoutCookieConnector;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Dialogs\GetDialogsRequest;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

beforeEach(function () {
    EnsureValidCookie::check();

    $this->connector = new DocuWareWithoutCookieConnector(config('docuware.cookies'));
});

it('can list dialogs for a file cabinet', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');

    $dialogs = $this->connector->send(new GetDialogsRequest($fileCabinetId))->dto();

    $this->assertInstanceOf(Collection::class, $dialogs);
    $this->assertNotCount(0, $dialogs);
    Event::assertDispatched(DocuWareResponseLog::class);
});
