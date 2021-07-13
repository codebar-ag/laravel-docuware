<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    '@PSR2' => true,
    'array_indentation' => true,
    'array_syntax' => ['syntax' => 'short'],
    'binary_operator_spaces' => true,
    'blank_line_before_statement' => [
        'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
    ],
    'cast_spaces' => ['space' => 'single'],
    'class_attributes_separation' => [
        'elements' => ['method' => 'one'],
    ],
    'concat_space' => ['spacing' => 'one'],
    'function_declaration' => false,
    'method_argument_space' => [
        'on_multiline' => 'ensure_fully_multiline',
        'keep_multiple_spaces_after_comma' => true,
    ],
    'method_chaining_indentation' => true,
    'no_empty_statement' => true,
    'no_unused_imports' => true,
    'not_operator_with_successor_space' => false,
    'ordered_imports' => ['sort_algorithm' => 'alpha'],
    'phpdoc_scalar' => true,
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_var_without_name' => true,
    'return_type_declaration' => true,
    'single_quote' => true,
    'single_trait_insert_per_statement' => true,
    'trailing_comma_in_multiline' => ['elements' => ['arrays', 'arguments', 'parameters']],
    'unary_operator_spaces' => true,
];

$finder = Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setRules($rules)
    ->setFinder($finder);
