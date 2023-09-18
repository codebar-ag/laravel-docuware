<?php

use CodebarAg\DocuWare\Connectors\DocuWareWithoutCookieConnector;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\FileCabinets\GetFileCabinetsRequest;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

uses()->group('docuware');

beforeEach(function () {
	EnsureValidCookie::check();

	$this->connector = new DocuWareWithoutCookieConnector(config('docuware.cookies'));
});

it('can list file cabinets', function () {
	Event::fake();

	$fileCabinets = $this->connector->send(new GetFileCabinetsRequest())->dto();

	$this->assertInstanceOf(Collection::class, $fileCabinets);
	$this->assertNotCount(0, $fileCabinets);
	Event::assertDispatched(DocuWareResponseLog::class);
})->only();
