<?php

use CodebarAg\DocuWare\Exceptions\MalformedResponseException;

/**
 * Contract guardrail: every `*Data::fromDocuWare()` factory must tolerate a DocuWare response
 * that omits optional fields. It may return an object or throw a clear
 * {@see MalformedResponseException} for a genuinely-required identity field — but it must NEVER
 * leak a raw `TypeError`/`Error`. This single reflection-driven test covers the whole
 * deserialization layer and blocks the "non-nullable param fed from Arr::get()" bug class from
 * ever returning silently.
 *
 * @return list<class-string>
 */
function allFromDocuWareClasses(): array
{
    $base = realpath(__DIR__.'/../../../src/Data');
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
    );

    $classes = [];

    /** @var SplFileInfo $file */
    foreach ($iterator as $file) {
        if ($file->getExtension() !== 'php') {
            continue;
        }

        $relative = str_replace([$base.DIRECTORY_SEPARATOR, '.php'], '', $file->getPathname());
        $class = 'CodebarAg\\DocuWare\\Data\\'.str_replace(DIRECTORY_SEPARATOR, '\\', $relative);

        if (! class_exists($class)) {
            continue;
        }

        $reflection = new ReflectionClass($class);

        if ($reflection->isAbstract() || ! $reflection->hasMethod('fromDocuWare')) {
            continue;
        }

        $method = $reflection->getMethod('fromDocuWare');

        // Only the single-array-argument factories — the canonical deserialization entry point.
        if (! $method->isStatic() || $method->getNumberOfRequiredParameters() !== 1) {
            continue;
        }

        $classes[] = $class;
    }

    sort($classes);

    return $classes;
}

dataset('data classes', allFromDocuWareClasses());

it('finds data classes to check', function () {
    expect(allFromDocuWareClasses())->not->toBeEmpty();
});

it('tolerates an empty payload without a TypeError', function (string $class) {
    try {
        $result = $class::fromDocuWare([]);
        expect($result)->toBeInstanceOf($class);
    } catch (MalformedResponseException) {
        // Acceptable: a required identity field was absent — clear, catchable, named.
        expect(true)->toBeTrue();
    }
})->with('data classes');

it('tolerates a partial payload with mistyped containers', function (string $class) {
    // Fields DocuWare would nest as objects/lists, deliberately given wrong scalar types.
    $payload = [
        'Id' => 'x', 'FieldName' => 'X', 'Name' => 'X', 'DBName' => 'X',
        'Fields' => 'not-an-array', 'Sections' => 'x', 'Links' => 'x',
        'Item' => 'x', 'Row' => 'x', 'HistorySteps' => 'x', 'Info' => 'x',
    ];

    try {
        expect($class::fromDocuWare($payload))->toBeInstanceOf($class);
    } catch (MalformedResponseException) {
        expect(true)->toBeTrue();
    }
})->with('data classes');
