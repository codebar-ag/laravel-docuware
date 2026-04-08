<?php

namespace CodebarAg\DocuWare\Support;

final class JsonArrays
{
    /**
     * Normalize a single JSON object/array to an associative row with string keys.
     *
     * @return array<string, mixed>
     */
    public static function associativeRow(mixed $raw): array
    {
        if (! is_array($raw)) {
            return [];
        }

        return self::stringKeyedRow($raw);
    }

    /**
     * Normalize mixed JSON (e.g. decoded list or map of rows) to a list of associative records.
     *
     * @return list<array<string, mixed>>
     */
    public static function listOfRecords(mixed $raw): array
    {
        if (! is_array($raw)) {
            return [];
        }

        $out = [];
        foreach (array_values($raw) as $item) {
            if (! is_array($item)) {
                continue;
            }

            $out[] = self::stringKeyedRow($item);
        }

        return $out;
    }

    /**
     * @param  array<mixed>  $row
     * @return array<string, mixed>
     */
    private static function stringKeyedRow(array $row): array
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            $normalized[(string) $key] = $value;
        }

        return $normalized;
    }
}
