<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

final class UnableToGetDocumentCount extends RuntimeException
{
    public static function noGroupKey(): self
    {
        return new self('There was no Group key provided in the response.');
    }

    public static function noGroupKeyIndexZero(): self
    {
        return new self('There was no Group key with an index of zero provided in the response.');
    }

    public static function noCount(): self
    {
        return new self('There was no Count key provided in the response.');
    }
}
