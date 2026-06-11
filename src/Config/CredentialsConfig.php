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

    public function grant(): Grant
    {
        return Grant::Credentials;
    }

    public function identifier(): string
    {
        return hash('sha256', $this->name.'|'.$this->url.'|'.$this->username.'|'.$this->password);
    }
}
