<?php

use CodebarAg\DocuWare\Data\DocuWareData;
use CodebarAg\DocuWare\Events\ResponseReceived;
use CodebarAg\DocuWare\Exceptions\BadRequestException;
use CodebarAg\DocuWare\Exceptions\DocuWareException;
use CodebarAg\DocuWare\Requests\General\Organization\GetOrganization;
use CodebarAg\DocuWare\Resources\Resource;
use Illuminate\Support\Facades\Event;
use Saloon\Http\Faking\MockResponse;

const SENTINEL = 'sup3r-s3cret-sentinel-value';

beforeEach(function () {
    config()->set('laravel-docuware.default', 'default');
    config()->set('laravel-docuware.instances.default', [
        'grant' => 'credentials', 'url' => 'https://example.docuware.cloud',
        'username' => 'alice', 'password' => 'secret',
    ]);
    config()->set('laravel-docuware.configurations.cache.driver', 'array');
    config()->set('laravel-docuware.configurations.cache.lifetime_in_seconds', 60);
    config()->set('laravel-docuware.configurations.request.timeout_in_seconds', 30);
    config()->set('laravel-docuware.configurations.client_id', 'x');
    config()->set('laravel-docuware.configurations.scope', 'y');
    config()->set('laravel-docuware.retry.enabled', false);
});

it('never leaks secrets through the response event, even with body capture on', function () {
    config()->set('laravel-docuware.debug.capture_bodies', true);

    $captured = [];
    Event::listen(ResponseReceived::class, function (ResponseReceived $e) use (&$captured) {
        $captured[] = $e;
    });

    $client = clientWithMock([
        GetOrganization::class => MockResponse::make(
            ['Organization' => [], 'access_token' => SENTINEL],
            200,
            ['Authorization' => 'Bearer '.SENTINEL, 'Set-Cookie' => SENTINEL],
        ),
    ]);

    $client->organizations()->all();

    expect($captured)->not->toBeEmpty();
    $serialized = json_encode(array_map(fn (ResponseReceived $e) => [
        $e->headers, $e->body, $e->method, $e->uri, $e->requestId,
    ], $captured));

    expect($serialized)->not->toContain(SENTINEL);
});

it('never leaks secrets through a thrown exception', function () {
    $client = clientWithMock([
        GetOrganization::class => MockResponse::make(
            ['Message' => 'failed: access_token='.SENTINEL],
            400,
            ['Authorization' => 'Bearer '.SENTINEL],
        ),
    ]);

    try {
        $client->organizations()->all();
        $this->fail('Expected an exception.');
    } catch (BadRequestException $e) {
        $serialized = json_encode([$e->getMessage(), $e->docuwareMessage, $e->context()]);
        expect($serialized)->not->toContain(SENTINEL);
    }
});

it('roots every package exception at DocuWareException', function () {
    expect('CodebarAg\DocuWare\Exceptions\NotFoundException')->toExtend(DocuWareException::class);
    expect('CodebarAg\DocuWare\Exceptions\AuthenticationException')->toExtend(DocuWareException::class);
    expect('CodebarAg\DocuWare\Exceptions\RateLimitException')->toExtend(DocuWareException::class);
});

it('keeps the resource gateways extending the base Resource', function () {
    expect('CodebarAg\DocuWare\Resources')
        ->classes
        ->toExtend(Resource::class)
        ->ignoring(['CodebarAg\DocuWare\Resources\Resource', 'CodebarAg\DocuWare\Resources\Search']);
});

it('keeps response data objects immutable-by-convention under DocuWareData', function () {
    expect('CodebarAg\DocuWare\Data\Documents\DocumentData')->toExtend(DocuWareData::class);
    expect('CodebarAg\DocuWare\Data\Organization\OrganizationData')->toExtend(DocuWareData::class);
});
