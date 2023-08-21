<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

final class UnableToUpdateFields extends RuntimeException
{
    public static function noValuesProvided(): self
    {
        return new self('Ensure to provide at least 1 field and value.');
    }

    public static function noValuesProvidedForField(string $field): self
    {
        return new self("Ensure to provide a value for field {$field}.");
    }
}
