<?php

namespace CodebarAg\DocuWare\Tests\Support;

use Saloon\Enums\Method;
use Saloon\Http\SoloRequest;

/**
 * Minimal solo request used to obtain a real Saloon Response (via MockClient) in unit tests.
 */
final class PlainSoloRequest extends SoloRequest
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return 'https://fixture.docuware.test/x';
    }
}
