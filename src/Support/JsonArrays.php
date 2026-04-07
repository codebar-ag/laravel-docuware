<?php

namespace CodebarAg\DocuWare\Support;

final class JsonArrays
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function listOfRecords(mixed $raw): array
    {
        if (! is_array($raw)) {
            return [];
        }

        $out = [];
        foreach (array_values($raw) as $item) {
            if (is_array($item)) {
                $out[] = $item;
            }
        }

        return $out;
    }
}
