<?php

namespace codebar\DocuWare;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use codebar\DocuWare\Commands\DocuWareCommand;

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
            ->hasViews()
            ->hasMigration('create_laravel_docuware_table')
            ->hasCommand(DocuWareCommand::class);
    }
}
