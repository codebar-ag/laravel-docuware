<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

class UnableToSearchDocuments extends RuntimeException
{
    public static function cabinetNotSet(): self
    {
        return new static(
            'You need to specify the file cabinet id. ' .
                'Try to chain `->fileCabinet($id)`',
        );
    }

    public static function dialogNotSet(): self
    {
        return new static(
            'You need to specify the dialog id. ' .
                'Try to chain `->dialog($id)`',
        );
    }
}
