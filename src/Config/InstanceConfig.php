<?php

namespace CodebarAg\DocuWare\Config;

use CodebarAg\DocuWare\DocuWareManager;
use CodebarAg\DocuWare\Enums\Grant;
use InvalidArgumentException;

/**
 * Typed, validated configuration for a single DocuWare instance (tenant/environment).
 *
 * Pure value object — it reads nothing global. The {@see DocuWareManager}
 * resolves and merges config, then hands a complete attribute array to {@see self::make()}.
 * Secrets are never echoed in validation errors (only key names are mentioned).
 */
abstract class InstanceConfig
{
    public function __construct(
        public readonly string $name,
        public readonly string $url,
        public readonly ?string $passphrase,
        public readonly string $cacheDriver,
        public readonly int $cacheLifetimeInSeconds,
        public readonly int $requestTimeoutInSeconds,
        public readonly string $clientId,
        public readonly string $scope,
    ) {}

    abstract public function grant(): Grant;

    /**
     * Stable per-instance identifier — used for cache/token-store namespacing. Includes the
     * instance name so two tenants sharing a URL never collide.
     */
    abstract public function identifier(): string;

    /**
     * Build the concrete config for the instance's grant from a fully-merged attribute array.
     *
     * @param  array<string, mixed>  $c
     */
    public static function make(string $name, array $c): self
    {
        $grant = $c['grant'] instanceof Grant
            ? $c['grant']
            : Grant::tryFrom((string) ($c['grant'] ?? Grant::Credentials->value))
                ?? throw new InvalidArgumentException("Instance [{$name}] has an unknown grant [{$c['grant']}].");

        $common = [
            'url' => self::required($name, $c, 'url'),
            'passphrase' => self::nullableString($c['passphrase'] ?? null),
            'cacheDriver' => (string) ($c['cacheDriver'] ?? 'file'),
            'cacheLifetimeInSeconds' => (int) ($c['cacheLifetimeInSeconds'] ?? 60),
            'requestTimeoutInSeconds' => (int) ($c['requestTimeoutInSeconds'] ?? 60),
            'clientId' => (string) ($c['clientId'] ?? 'docuware.platform.net.client'),
            'scope' => (string) ($c['scope'] ?? 'docuware.platform'),
        ];

        return match ($grant) {
            Grant::Credentials => new CredentialsConfig(
                ...$common,
                name: $name,
                username: self::required($name, $c, 'username'),
                password: self::required($name, $c, 'password'),
            ),
            Grant::TrustedUser => new TrustedUserConfig(
                ...$common,
                name: $name,
                username: self::required($name, $c, 'username'),
                password: self::required($name, $c, 'password'),
                impersonatedUsername: self::required($name, $c, 'impersonate'),
            ),
            Grant::Token => new TokenConfig(
                ...$common,
                name: $name,
                token: self::required($name, $c, 'token'),
                username: (string) ($c['username'] ?? ''),
            ),
        };
    }

    /**
     * @param  array<string, mixed>  $c
     */
    protected static function required(string $name, array $c, string $key): string
    {
        $value = $c[$key] ?? null;

        if (! is_string($value) || $value === '') {
            throw new InvalidArgumentException(
                "DocuWare instance [{$name}] is missing required config key [{$key}]."
            );
        }

        return $value;
    }

    protected static function nullableString(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }
}
