<?php

namespace CodebarAg\DocuWare\Data\Support;

use CodebarAg\DocuWare\Exceptions\MalformedResponseException;
use Illuminate\Support\Arr;

/**
 * Null-safe field extraction for `*Data::fromDocuWare()` factories.
 *
 * DocuWare omits optional/variant fields from responses, and `Arr::get()` returns null for a
 * missing key — feeding null into a non-nullable typed constructor param throws a bare
 * `TypeError`. These helpers make the deserialization layer match the API contract: required
 * identity fields raise a clear {@see MalformedResponseException} naming the field, while
 * optional fields coerce safely (or return null) instead of crashing.
 */
final class Field
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function string(array $data, string $key, ?string $context = null): string
    {
        $value = Arr::get($data, $key);

        if (! is_string($value) && ! is_int($value) && ! is_float($value)) {
            throw MalformedResponseException::missingField($key, $context);
        }

        return (string) $value;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function int(array $data, string $key, ?string $context = null): int
    {
        $value = Arr::get($data, $key);

        if (! is_numeric($value)) {
            throw MalformedResponseException::missingField($key, $context);
        }

        return (int) $value;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function stringOrNull(array $data, string $key): ?string
    {
        $value = Arr::get($data, $key);

        return is_string($value) || is_int($value) || is_float($value) ? (string) $value : null;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function intOrNull(array $data, string $key): ?int
    {
        $value = Arr::get($data, $key);

        return is_numeric($value) ? (int) $value : null;
    }

    /**
     * Coerce to bool, tolerating the string `"true"`/`"false"` the XML response path yields.
     *
     * @param  array<string, mixed>  $data
     */
    public static function bool(array $data, string $key, bool $default = false): bool
    {
        $value = Arr::get($data, $key);

        return match (true) {
            $value === null => $default,
            is_bool($value) => $value,
            is_string($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            default => (bool) $value,
        };
    }

    /**
     * Nullable bool — distinguishes a present `false` from an absent field.
     *
     * @param  array<string, mixed>  $data
     */
    public static function boolOrNull(array $data, string $key): ?bool
    {
        $value = Arr::get($data, $key);

        return match (true) {
            $value === null => null,
            is_bool($value) => $value,
            is_string($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            default => (bool) $value,
        };
    }
}
