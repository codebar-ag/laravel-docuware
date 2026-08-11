<?php

use CodebarAg\DocuWare\Data\Authentication\RequestTokenData;
use CodebarAg\DocuWare\DocuWareManager;
use CodebarAg\DocuWare\Requests\Documents\DocumentsTrashBin\DeleteDocuments;
use CodebarAg\DocuWare\Requests\General\Organization\GetOrganization;
use CodebarAg\DocuWare\Requests\Search\GetSearchRequest;
use CodebarAg\DocuWare\Transport\DocuWareConnector;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    config()->set('laravel-docuware.default', 'default');
    config()->set('laravel-docuware.instances.default', [
        'grant' => 'credentials',
        'url' => 'https://example.docuware.cloud',
        'username' => 'alice',
        'password' => 'secret',
    ]);
    config()->set('laravel-docuware.configurations.cache.driver', 'array');
    config()->set('laravel-docuware.retry', [
        'enabled' => true,
        'times' => 2,
        'base_interval_ms' => 0,
        'max_interval_ms' => 0,
    ]);
});

function retryConnector(): DocuWareConnector
{
    $config = app(DocuWareManager::class)->resolveConfig('default');

    $token = new RequestTokenData(
        accessToken: 'seeded-token',
        tokenType: 'Bearer',
        scope: 'docuware.platform',
        expiresIn: 3600,
        expiresAt: Carbon::now()->addHour(),
    );

    Cache::store('array')->put('docuware.oauth.'.$config->identifier(), Crypt::encrypt($token), 3600);

    return app(DocuWareManager::class)->instance('default')->connector();
}

it('retries the POST dialog-expression search on connection errors', function () {
    $connector = retryConnector();
    $request = new GetSearchRequest('cabinet-id');

    $exception = new FatalRequestException(
        new Exception('cURL error 28: Operation timed out'),
        $connector->createPendingRequest($request),
    );

    expect($connector->handleRetry($exception, $request))->toBeTrue();
});

it('does not retry mutating POST requests on connection errors', function () {
    $connector = retryConnector();
    $request = new DeleteDocuments(['1']);

    $exception = new FatalRequestException(
        new Exception('cURL error 28: Operation timed out'),
        $connector->createPendingRequest($request),
    );

    expect($connector->handleRetry($exception, $request))->toBeFalse();
});

it('retries idempotent verbs on connection errors', function () {
    $connector = retryConnector();
    $request = new GetOrganization;

    $exception = new FatalRequestException(
        new Exception('cURL error 28: Operation timed out'),
        $connector->createPendingRequest($request),
    );

    expect($connector->handleRetry($exception, $request))->toBeTrue();
});

it('retries the POST dialog-expression search on 5xx and succeeds on the next attempt', function () {
    $connector = retryConnector();

    $mockClient = new MockClient([
        MockResponse::make(['Message' => 'boom'], 500),
        MockResponse::make(['Items' => [], 'Count' => ['Value' => 0]], 200),
    ]);
    $connector->withMockClient($mockClient);

    $response = $connector->send(new GetSearchRequest('cabinet-id'));

    expect($response->status())->toBe(200);
    $mockClient->assertSentCount(2);
});

it('does not retry mutating POST requests on 5xx', function () {
    $connector = retryConnector();

    $mockClient = new MockClient([
        MockResponse::make(['Message' => 'boom'], 500),
    ]);
    $connector->withMockClient($mockClient);

    $response = $connector->send(new DeleteDocuments(['1']));

    expect($response->status())->toBe(500);
    $mockClient->assertSentCount(1);
});
