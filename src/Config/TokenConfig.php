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

    /**
     * Build a token-grant config at runtime from a DocuWare login token. Defaults
     * mirror {@see InstanceConfig::make()}.
     */
    public static function for(
        string $url,
        string $token,
        string $name = 'runtime',
        string $username = '',
        ?string $passphrase = null,
        string $cacheDriver = self::DEFAULT_CACHE_DRIVER,
        int $cacheLifetimeInSeconds = self::DEFAULT_CACHE_LIFETIME_IN_SECONDS,
        int $requestTimeoutInSeconds = self::DEFAULT_REQUEST_TIMEOUT_IN_SECONDS,
        string $clientId = self::DEFAULT_CLIENT_ID,
        string $scope = self::DEFAULT_SCOPE,
    ): self {
        return new self(
            name: $name,
            token: $token,
            username: $username,
            url: $url,
            passphrase: $passphrase,
            cacheDriver: $cacheDriver,
            cacheLifetimeInSeconds: $cacheLifetimeInSeconds,
            requestTimeoutInSeconds: $requestTimeoutInSeconds,
            clientId: $clientId,
            scope: $scope,
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
