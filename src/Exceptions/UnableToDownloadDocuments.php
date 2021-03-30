<?php

namespace codebar\DocuWare\Exceptions;

use RuntimeException;

class UnableToDownloadDocuments extends RuntimeException
{
    public static function selectAtLeastTwoDocuments(): self
    {
        return new self(
            'You have to select at least two documents to download multiple documents at once.',
        );
    }
}
