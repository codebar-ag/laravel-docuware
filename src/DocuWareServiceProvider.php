<?php

namespace CodebarAg\DocuWare;

use Illuminate\Http\Client\Response;
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
            ->hasConfigFile();
    }

    public static function logResponse(Response $response): void
    {
        // ray($response->effectiveUri()->__toString());
    }
}
