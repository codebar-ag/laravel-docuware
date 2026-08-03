<?php

namespace CodebarAg\DocuWare\Client;

use CodebarAg\DocuWare\Concerns\InteractsWithResources;
use CodebarAg\DocuWare\Config\InstanceConfig;
use CodebarAg\DocuWare\DocuWareManager;
use CodebarAg\DocuWare\Transport\Auth\OAuthTokenFetcher;
use CodebarAg\DocuWare\Transport\Auth\TokenRepository;
use CodebarAg\DocuWare\Transport\DocuWareConnector;

/**
 * A single resolved DocuWare instance: owns one connector (one Guzzle handler → connection
 * reuse) and exposes the resource gateways. One client per instance is cached by the
 * {@see DocuWareManager}.
 */
final class DocuWareClient
{
    use InteractsWithResources;

    private ?DocuWareConnector $connector = null;

    public function __construct(
        public readonly InstanceConfig $config,
        private readonly TokenRepository $tokens,
        private readonly OAuthTokenFetcher $fetcher,
    ) {}

    public function name(): string
    {
        return $this->config->name;
    }

    protected function resourceClient(): self
    {
        return $this;
    }

    public function connector(): DocuWareConnector
    {
        return $this->connector ??= new DocuWareConnector($this->config, $this->tokens, $this->fetcher);
    }
}
