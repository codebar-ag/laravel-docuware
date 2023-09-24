<?php

use CodebarAg\DocuWare\Connectors\DocuWareStaticConnector;
use CodebarAg\DocuWare\DTO\Config;
use CodebarAg\DocuWare\DTO\Organization;
use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\Organization\GetOrganizationRequest;
use CodebarAg\DocuWare\Requests\Organization\GetOrganizationsRequest;
use CodebarAg\DocuWare\Support\EnsureValidCookie;
use Illuminate\Support\Collection;
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

it('can list organizations', function () {
    Event::fake();

    $organizations = $this->connector->send(new GetOrganizationsRequest())->dto();

    $this->assertInstanceOf(Collection::class, $organizations);
    $this->assertNotCount(0, $organizations);
    Event::assertDispatched(DocuWareResponseLog::class);
});

it('can get an organization', function () {
    Event::fake();

    $orgID = config('docuware.tests.organization_id');

    $organization = $this->connector->send(new GetOrganizationRequest($orgID))->dto();

    $this->assertInstanceOf(Organization::class, $organization);
    Event::assertDispatched(DocuWareResponseLog::class);
});
