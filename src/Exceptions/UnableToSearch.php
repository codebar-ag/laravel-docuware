<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

class UnableToSearch extends RuntimeException
{
    public static function cabinetNotSet(): self
    {
        return new static(
            'You need to specify the file cabinet id. '.
            'Try to chain: "->fileCabinet($id)"',
        );
    }

    public static function invalidPageNumber(int $page): self
    {
        return new static(
            'You need to specify page number greater than zero. '.
            "Following is not valid: \"->page({$page})\"",
        );
    }

    public static function invalidPerPageNumber(int $perPage): self
    {
        return new static(
            'You need to specify per page number greater than zero. '.
            "Following is not valid: \"->perPage({$perPage})\"",

        );
    }
}
