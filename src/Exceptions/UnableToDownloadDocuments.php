<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

class UnableToDownloadDocuments extends RuntimeException
{
    public static function selectAtLeastTwoDocuments(): self
    {
        return new static('Ensure to select at least two documents.');
    }
}
