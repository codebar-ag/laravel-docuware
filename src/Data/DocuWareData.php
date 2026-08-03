<?php

namespace CodebarAg\DocuWare\Data;

use ReflectionClass;
use Spatie\LaravelData\Data;

/**
 * Base for every DocuWare response/value object.
 *
 * Extends spatie/laravel-data for serialization (toArray/toJson), collection helpers and
 * Laravel response integration, while the bespoke DocuWare wire format (PascalCase keys,
 * `/Date(ms)/` timestamps, derived fields) is mapped by each class's own static
 * `fromDocuWare()` method — the single deserialization path the thin requests call.
 *
 * Immutable by convention; {@see self::copyWith()} returns a modified copy (laravel-data's
 * own `with()` is reserved for appended data, so the wither is named differently).
 */
abstract class DocuWareData extends Data
{
    /**
     * Return a new instance with the given constructor properties overridden by name:
     * `$user->copyWith(active: false)`. Reconstructs via the constructor so readonly
     * properties and value objects are preserved exactly.
     */
    public function copyWith(mixed ...$overrides): static
    {
        $reflection = new ReflectionClass($this);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return clone $this;
        }

        $args = [];
        foreach ($constructor->getParameters() as $parameter) {
            $name = $parameter->getName();
            $args[] = array_key_exists($name, $overrides)
                ? $overrides[$name]
                : $this->{$name};
        }

        return $reflection->newInstanceArgs($args);
    }
}
