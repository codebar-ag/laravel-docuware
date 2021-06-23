<?php

namespace CodebarAg\DocuWare\Exceptions;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;
use RuntimeException;

class UnableToFindPasswordCredential extends RuntimeException implements ProvidesSolution
{
    public static function create(): self
    {
        return new static('Your password is not found.');
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create('Try to add following in your .env-file:')
            ->setSolutionDescription('DOCUWARE_PASSWORD=password')
            ->setDocumentationLinks([
                'GitHub' => 'https://github.com/codebar-ag/laravel-docuware#installation',
            ]);
    }
}
