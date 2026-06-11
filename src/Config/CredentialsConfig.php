<?php

namespace CodebarAg\DocuWare\Config;

use CodebarAg\DocuWare\Enums\Grant;

/**
 * Username/password grant (`grant_type=password`).
 */
final class CredentialsConfig extends InstanceConfig
{
    public function __construct(
        string $name,
        public readonly string $username,
        public readonly string $password,
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
     * Build a credentials config at runtime — the ergonomic, IDE-friendly path for
     * passing a tenant connection as a DTO (e.g. resolved from a database) without
     * touching the published config file. Defaults mirror {@see InstanceConfig::make()}.
     */
    public static function for(
        string $url,
        string $username,
        string $password,
        string $name = 'runtime',
        ?string $passphrase = null,
        string $cacheDriver = self::DEFAULT_CACHE_DRIVER,
        int $cacheLifetimeInSeconds = self::DEFAULT_CACHE_LIFETIME_IN_SECONDS,
        int $requestTimeoutInSeconds = self::DEFAULT_REQUEST_TIMEOUT_IN_SECONDS,
        string $clientId = self::DEFAULT_CLIENT_ID,
        string $scope = self::DEFAULT_SCOPE,
    ): self {
        return new self(
            name: $name,
            username: $username,
            password: $password,
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
        return Grant::Credentials;
    }

    public function identifier(): string
    {
        return hash('sha256', $this->name.'|'.$this->url.'|'.$this->username.'|'.$this->password);
    }
}
