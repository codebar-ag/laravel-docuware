<?php

use CodebarAg\DocuWare\Events\DocuWareResponseLog;
use CodebarAg\DocuWare\Requests\General\Organization\GetOrganization;
use CodebarAg\DocuWare\Tests\Support\FixtureDocuWareConnector;
use Illuminate\Support\Facades\Event;
use Saloon\Http\Faking\Fixture;
use Saloon\Http\Faking\MockClient;

it('maps GetOrganization through a Saloon fixture file', function () {
    Event::fake();

    $mockClient = new MockClient([
        GetOrganization::class => new Fixture('get-organization'),
    ]);

    $connector = (new FixtureDocuWareConnector)->withMockClient($mockClient);

    $organizations = $connector->send(new GetOrganization)->dto();

    // Asserts the DTO mapping against the recorded fixture; structural so the test
    // survives re-recording the fixture against any tenant (DOCUWARE_RECORD_FIXTURES=true).
    expect($organizations)->not->toBeEmpty();

    $organization = $organizations->first();
    expect($organization->id)->toBeString()->not->toBeEmpty()
        ->and($organization->name)->toBeString()->not->toBeEmpty();

    Event::assertDispatched(DocuWareResponseLog::class);
});
