<?php

namespace CodebarAg\DocuWare\Exceptions;

use Spatie\Ignition\Contracts\BaseSolution;
use Spatie\Ignition\Contracts\ProvidesSolution;
use Spatie\Ignition\Contracts\Solution;
use RuntimeException;

class UnableToFindUrlCredential extends RuntimeException implements ProvidesSolution
{
    public static function create(): self
    {
        return new static('Your URL is not found.');
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create('Try to add following in your .env-file:')
            ->setSolutionDescription('DOCUWARE_URL=https://domain.docuware.cloud')
            ->setDocumentationLinks([
                'GitHub' => 'https://github.com/codebar-ag/laravel-docuware#installation',
            ]);
    }
}
