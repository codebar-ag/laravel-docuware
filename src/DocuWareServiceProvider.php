<?php

namespace CodebarAg\DocuWare;

use CodebarAg\DocuWare\Console\ListAuthCookie;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DocuWareServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-docuware')
            ->hasConfigFile()
            ->hasCommand(ListAuthCookie::class);
    }
}
