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

    expect($organizations)->toHaveCount(1);
    expect($organizations->first()->name)->toBe('Fixture Org');
    expect($organizations->first()->id)->toBe('org-fixture-1');

    Event::assertDispatched(DocuWareResponseLog::class);
});
