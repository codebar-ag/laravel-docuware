<?php

namespace CodebarAg\DocuWare;

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
}
