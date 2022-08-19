<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/examples')
    ->in(__DIR__ . '/tests');

$config = new PhpCsFixer\Config();
return $config
    ->setRules([
        '@PSR2' => true,
        'blank_line_after_opening_tag' => false,
        'linebreak_after_opening_tag' => false,
        'no_superfluous_phpdoc_tags' => false,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'binary_operator_spaces' => [
            'operators' => [
                '=>' => 'align',
                '=' => 'single_space',
            ],
        ],
        'simplified_null_return' => true,
        'blank_line_after_namespace' => true,
        'function_typehint_space' => true,
        'multiline_comment_opening_closing' => true,
        'no_unused_imports' => true,
        'single_line_after_imports' => true,
        'fully_qualified_strict_types' => true,
    ])
    ->setUsingCache(false)
    ->setFinder($finder);
