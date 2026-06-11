<?php

namespace CodebarAg\DocuWare;

use CodebarAg\DocuWare\Client\DocuWareClient;
use CodebarAg\DocuWare\Concerns\InteractsWithResources;
use CodebarAg\DocuWare\Config\InstanceConfig;
use CodebarAg\DocuWare\Transport\Auth\OAuthTokenFetcher;
use CodebarAg\DocuWare\Transport\Auth\TokenRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use InvalidArgumentException;

/**
 * Entry point for multi-instance DocuWare access. Mirrors Laravel's Manager: resolves a named
 * instance to a cached {@see DocuWareClient}. Single-app users never touch it directly — the
 * facade proxies bare resource calls to the default instance.
 */
final class DocuWareManager
{
    use InteractsWithResources;

    /** @var array<string, DocuWareClient> */
    private array $clients = [];

    public function __construct(
        private readonly ConfigRepository $config,
        private readonly TokenRepository $tokens,
        private readonly OAuthTokenFetcher $fetcher,
    ) {}

    /**
     * Resolve a client for an instance. Pass a config-file instance name (cached for the
     * request), an {@see InstanceConfig} DTO built at runtime (see {@see self::connection()}),
     * or nothing for the default instance.
     */
    public function instance(string|InstanceConfig|null $instance = null): DocuWareClient
    {
        if ($instance instanceof InstanceConfig) {
            return $this->connection($instance);
        }

        $name = $instance ?? $this->getDefaultInstance();

        return $this->clients[$name] ??= new DocuWareClient(
            $this->resolveConfig($name),
            $this->tokens,
            $this->fetcher,
        );
    }

    /**
     * Build a client from a runtime {@see InstanceConfig} DTO — the ergonomic path for
     * database-driven multi-tenancy where connections are not declared in config files.
     * Clients are not name-cached here (two tenants may share a name); per-tenant tokens
     * stay isolated via {@see InstanceConfig::identifier()} in the token store.
     */
    public function connection(InstanceConfig $config): DocuWareClient
    {
        return new DocuWareClient($config, $this->tokens, $this->fetcher);
    }

    public function getDefaultInstance(): string
    {
        return (string) $this->config->get('laravel-docuware.default', 'default');
    }

    /**
     * Build an encrypted DocuWare integration URL for direct document access.
     */
    public function url(string $url, string $username, string $password, ?string $passphrase = null): DocuWareUrl
    {
        return new DocuWareUrl($url, $username, $password, $passphrase);
    }

    /**
     * Resource accessors (via InteractsWithResources) operate on the default instance.
     */
    protected function resourceClient(): DocuWareClient
    {
        return $this->instance();
    }

    public function resolveConfig(string $name): InstanceConfig
    {
        return InstanceConfig::make($name, $this->instanceAttributes($name));
    }

    /**
     * Drop a cached client (or all of them) — forces re-resolution on next access.
     */
    public function forget(?string $name = null): void
    {
        if ($name === null) {
            $this->clients = [];

            return;
        }

        unset($this->clients[$name]);
    }

    /**
     * Merge the instance's config block with global defaults into the flat attribute array
     * {@see InstanceConfig::make()} expects. Falls back to the legacy flat keys for the
     * default instance when no `instances` entry exists.
     *
     * @return array<string, mixed>
     */
    private function instanceAttributes(string $name): array
    {
        $instances = (array) $this->config->get('laravel-docuware.instances', []);
        $c = (array) ($instances[$name] ?? []);

        if ($c === [] && $name === 'default') {
            $c = [
                'grant' => 'credentials',
                'url' => $this->config->get('laravel-docuware.credentials.url'),
                'username' => $this->config->get('laravel-docuware.credentials.username'),
                'password' => $this->config->get('laravel-docuware.credentials.password'),
                'passphrase' => $this->config->get('laravel-docuware.passphrase'),
                'client_id' => $this->config->get('laravel-docuware.configurations.client_id'),
                'scope' => $this->config->get('laravel-docuware.configurations.scope'),
            ];
        }

        if ($c === []) {
            throw new InvalidArgumentException("DocuWare instance [{$name}] is not configured.");
        }

        return array_merge($c, [
            'url' => $c['url'] ?? $this->config->get('laravel-docuware.credentials.url'),
            'passphrase' => $c['passphrase'] ?? $this->config->get('laravel-docuware.passphrase'),
            'cacheDriver' => $c['cache_driver']
                ?? $this->config->get('laravel-docuware.configurations.cache.driver')
                ?? 'file',
            'cacheLifetimeInSeconds' => $c['cache_lifetime']
                ?? $this->config->get('laravel-docuware.configurations.cache.lifetime_in_seconds')
                ?? 60,
            'requestTimeoutInSeconds' => $c['timeout']
                ?? $this->config->get('laravel-docuware.configurations.request.timeout_in_seconds')
                ?? 60,
            'clientId' => $c['client_id']
                ?? $this->config->get('laravel-docuware.configurations.client_id')
                ?? 'docuware.platform.net.client',
            'scope' => $c['scope']
                ?? $this->config->get('laravel-docuware.configurations.scope')
                ?? 'docuware.platform',
        ]);
    }
}
