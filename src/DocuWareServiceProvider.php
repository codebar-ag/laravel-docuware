<?php

namespace CodebarAg\DocuWare;

use CodebarAg\DocuWare\Commands\ListAuthCookie;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DocuWareServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-docuware')
            ->hasConfigFile()
            ->hasCommand(ListAuthCookie::class);
    }
}
