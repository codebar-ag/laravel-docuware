<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

final class UnableToDownloadDocuments extends RuntimeException
{
    public static function selectAtLeastTwoDocuments(): self
    {
        return new self('Ensure to select at least two documents.');
    }
}
