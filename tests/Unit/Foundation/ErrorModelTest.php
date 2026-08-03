<?php

use CodebarAg\DocuWare\Exceptions\AuthenticationException;
use CodebarAg\DocuWare\Exceptions\BadRequestException;
use CodebarAg\DocuWare\Exceptions\ConflictException;
use CodebarAg\DocuWare\Exceptions\DocuWareException;
use CodebarAg\DocuWare\Exceptions\ForbiddenException;
use CodebarAg\DocuWare\Exceptions\NotFoundException;
use CodebarAg\DocuWare\Exceptions\RateLimitException;
use CodebarAg\DocuWare\Exceptions\RequestException;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetASpecificDocumentFromAFileCabinet;
use Saloon\Http\Faking\MockResponse;

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

function failWith(int $status, array $body = [], array $headers = []): void
{
    $client = clientWithMock([
        GetASpecificDocumentFromAFileCabinet::class => MockResponse::make($body ?: ['Message' => 'boom'], $status, $headers),
    ]);

    $client->documents('cab-1')->find(1);
}

it('maps statuses to the unified exception hierarchy', function (int $status, string $expected) {
    expect(fn () => failWith($status))->toThrow($expected);
})->with([
    [400, BadRequestException::class],
    [401, AuthenticationException::class],
    [403, ForbiddenException::class],
    [404, NotFoundException::class],
    [409, ConflictException::class],
    [429, RateLimitException::class],
    [418, RequestException::class],
    [500, DocuWareException::class],
]);

it('every typed exception descends from DocuWareException', function () {
    expect(fn () => failWith(404))->toThrow(DocuWareException::class);
});

it('carries redacted context (instance, status, docuware message)', function () {
    try {
        failWith(404, ['Message' => 'Document not found']);
    } catch (NotFoundException $e) {
        expect($e->instance)->toBe('default')
            ->and($e->statusCode)->toBe(404)
            ->and($e->docuwareMessage)->toBe('Document not found')
            ->and($e->context())->toHaveKeys(['instance', 'status', 'docuware_message', 'request_id']);

        return;
    }

    $this->fail('Expected NotFoundException was not thrown.');
});

it('redacts secrets that appear in an error message', function () {
    try {
        failWith(400, ['Message' => 'bad token access_token=s3cret-value here']);
    } catch (BadRequestException $e) {
        expect($e->docuwareMessage)->not->toContain('s3cret-value')
            ->and($e->docuwareMessage)->toContain('[REDACTED]');

        return;
    }

    $this->fail('Expected BadRequestException was not thrown.');
});
