<?php

namespace CodebarAg\DocuWare\Config;

use CodebarAg\DocuWare\Enums\Grant;

/**
 * DocuWare token grant (`grant_type=dwtoken`) — exchange a DocuWare login token for an
 * OAuth access token.
 */
final class TokenConfig extends InstanceConfig
{
    public function __construct(
        string $name,
        public readonly string $token,
        public readonly string $username,
        string $url,
        ?string $passphrase,
        string $cacheDriver,
        int $cacheLifetimeInSeconds,
        int $requestTimeoutInSeconds,
        string $clientId,
        string $scope,
    ) {
        parent::__construct(
            $name, $url, $passphrase, $cacheDriver,
            $cacheLifetimeInSeconds, $requestTimeoutInSeconds, $clientId, $scope,
        );
    }

    public function grant(): Grant
    {
        return Grant::Token;
    }

    public function identifier(): string
    {
        return hash('sha256', $this->name.'|'.$this->url.'|'.$this->token);
    }
}
