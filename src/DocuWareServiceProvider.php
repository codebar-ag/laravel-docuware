<?php

namespace CodebarAg\DocuWare;

use CodebarAg\DocuWare\Transport\Auth\EncryptedCacheTokenRepository;
use CodebarAg\DocuWare\Transport\Auth\OAuthTokenFetcher;
use CodebarAg\DocuWare\Transport\Auth\TokenRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DocuWareServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-docuware')
            ->hasConfigFile('laravel-docuware');
    }

    public function packageRegistered(): void
    {
        $this->app->bind(TokenRepository::class, EncryptedCacheTokenRepository::class);

        $this->app->singleton(DocuWareManager::class, fn ($app) => new DocuWareManager(
            $app->make(ConfigRepository::class),
            $app->make(TokenRepository::class),
            $app->make(OAuthTokenFetcher::class),
        ));
        $this->app->alias(DocuWareManager::class, 'docuware.manager');
    }
}
