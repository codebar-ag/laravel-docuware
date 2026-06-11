<?php

namespace CodebarAg\DocuWare\Exceptions;

use RuntimeException;

final class UnableToMakeUrl extends RuntimeException
{
    public static function documentNotSet(): self
    {
        return new self(
            'You need to specify the document id. '.
                'Try to chain: "->document($id)"',
        );
    }

    public static function sourceNotSet(): self
    {
        return new self(
            'You need to specify a file cabinet or basket id. '.
                'Try to chain: "->fileCabinet($id)" or "->basket($id)".',
        );
    }

    public static function passphraseNotSet(): self
    {
        return new self(
            'You need to provide a passphrase, either as the 4th argument to '.
                'DocuWare::url(...) or via the DOCUWARE_PASSPHRASE config value.',
        );
    }
}
