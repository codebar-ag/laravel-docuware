<?php

use CodebarAg\DocuWare\Connectors\DocuWareWithoutCookieConnector;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Fields\GetFieldsRequest;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

beforeEach(function () {
    EnsureValidCookie::check();

    $this->connector = new DocuWareWithoutCookieConnector(config('docuware.cookies'));
});

it('can list fields for a file cabinet', function () {
    Event::fake();

    $fileCabinetId = config('docuware.tests.file_cabinet_id');

    $fields = $this->connector->send(new GetFieldsRequest($fileCabinetId))->dto();

    $this->assertInstanceOf(Collection::class, $fields);
    $this->assertNotCount(0, $fields);
    Event::assertDispatched(DocuWareResponseLog::class);
});
