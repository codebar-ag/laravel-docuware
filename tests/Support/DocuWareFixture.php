<?php

namespace CodebarAg\DocuWare\Tests\Support;

use Saloon\Http\Faking\Fixture;

/**
 * Saloon fixture that redacts secrets before a recorded response is written to disk,
 * so committed fixtures never contain bearer tokens, cookies or identity tokens.
 */
final class DocuWareFixture extends Fixture
{
    /**
     * @return array<string, string>
     */
    protected function defineSensitiveHeaders(): array
    {
        return [
            'Authorization' => 'Bearer REDACTED',
            'Set-Cookie' => 'REDACTED',
            'Cookie' => 'REDACTED',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function defineSensitiveJsonParameters(): array
    {
        return [
            'access_token' => 'REDACTED',
            'refresh_token' => 'REDACTED',
            'IdentityToken' => 'REDACTED',
            'Token' => 'REDACTED',
        ];
    }
}
