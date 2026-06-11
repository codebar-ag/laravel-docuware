<?php

namespace CodebarAg\DocuWare\Config;

use CodebarAg\DocuWare\Enums\Grant;

/**
 * Trusted-user impersonation grant — authenticate as a trusted user, act as another.
 */
final class TrustedUserConfig extends InstanceConfig
{
    public function __construct(
        string $name,
        public readonly string $username,
        public readonly string $password,
        public readonly string $impersonatedUsername,
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
     * Build a trusted-user impersonation config at runtime. Defaults mirror
     * {@see InstanceConfig::make()}.
     */
    public static function for(
        string $url,
        string $username,
        string $password,
        string $impersonate,
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
            impersonatedUsername: $impersonate,
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
        return Grant::TrustedUser;
    }

    public function identifier(): string
    {
        return hash('sha256', $this->name.'|'.$this->url.'|'.$this->username.'|'.$this->impersonatedUsername.'|'.$this->password);
    }
}
