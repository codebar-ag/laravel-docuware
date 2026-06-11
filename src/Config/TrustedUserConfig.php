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

    public function grant(): Grant
    {
        return Grant::TrustedUser;
    }

    public function identifier(): string
    {
        return hash('sha256', $this->name.'|'.$this->url.'|'.$this->username.'|'.$this->impersonatedUsername.'|'.$this->password);
    }
}
