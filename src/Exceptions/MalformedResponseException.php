<?php

namespace CodebarAg\DocuWare\Exceptions;

/**
 * Thrown when a DocuWare response is missing a field the package requires to build a value
 * object (e.g. an identity field like `Id`). Replaces the bare `TypeError` that used to leak
 * out of the `*Data::fromDocuWare()` factories when the API omitted an expected key, so callers
 * get an actionable, catchable error naming the missing field instead.
 */
final class MalformedResponseException extends DocuWareException
{
    public static function missingField(string $field, ?string $context = null): self
    {
        $where = $context !== null ? " for {$context}" : '';

        return new self("DocuWare response missing or invalid required field [{$field}]{$where}.");
    }
}
