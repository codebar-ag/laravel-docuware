<?php

use CodebarAg\DocuWare\Exceptions\BadRequest;
use CodebarAg\DocuWare\Exceptions\Conflict;
use CodebarAg\DocuWare\Exceptions\Forbidden;
use CodebarAg\DocuWare\Exceptions\NotFound;
use CodebarAg\DocuWare\Exceptions\UnableToMakeRequest;
use CodebarAg\DocuWare\Exceptions\UnableToProcessRequest;
use CodebarAg\DocuWare\Support\EnsureValidResponse;
use CodebarAg\DocuWare\Tests\Support\PlainSoloRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;

/**
 * @param  array<string, mixed>|string  $body
 */
function responseWith(int $status, array|string $body = []): Response
{
    $mockClient = new MockClient([MockResponse::make($body, $status)]);

    return (new PlainSoloRequest)->withMockClient($mockClient)->send();
}

it('does not throw on a successful response', function () {
    expect(fn () => EnsureValidResponse::from(responseWith(200, ['ok' => true])))
        ->not->toThrow(Exception::class);
})->group('unit');

it('maps statuses to typed exceptions', function (int $status, string $exception) {
    expect(fn () => EnsureValidResponse::from(responseWith($status, ['Message' => 'boom'])))
        ->toThrow($exception);
})->with([
    [400, BadRequest::class],
    [401, UnableToMakeRequest::class],
    [403, Forbidden::class],
    [404, NotFound::class],
    [409, Conflict::class],
    [422, UnableToProcessRequest::class],
    [500, UnableToProcessRequest::class],
])->group('unit');

it('does not throw a JsonException when the error body is not JSON', function () {
    expect(fn () => EnsureValidResponse::from(responseWith(500, '<html><body>Account deactivated</body></html>')))
        ->toThrow(UnableToProcessRequest::class, 'Account deactivated');
})->group('unit');

it('surfaces the DocuWare Message field in the exception', function () {
    expect(fn () => EnsureValidResponse::from(responseWith(404, ['Message' => 'Document not found'])))
        ->toThrow(NotFound::class, 'Document not found');
})->group('unit');
