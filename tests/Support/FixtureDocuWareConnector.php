<?php

namespace CodebarAg\DocuWare\Tests\Support;

use CodebarAg\DocuWare\Connectors\DocuWareConnector;
use CodebarAg\DocuWare\DTO\Config\ConfigWithCredentials;
use Saloon\Http\Auth\TokenAuthenticator;

/**
 * Connector for Saloon fixture tests: skips OAuth and uses a static bearer token.
 */
final class FixtureDocuWareConnector extends DocuWareConnector
{
    public function __construct(?ConfigWithCredentials $configuration = null)
    {
        parent::__construct($configuration ?? new ConfigWithCredentials(
            username: 'fixture',
            password: 'fixture',
            url: 'https://fixture.docuware.test',
        ));
    }

    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator('fixture-token');
    }
}
