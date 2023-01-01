<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->exclude([
        __DIR__ . '/bootstrap',
        __DIR__ . '/public',
        __DIR__ . '/storage',
        __DIR__ . '/vendor',
    ])
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/lang',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ]);

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules([
        // 開始タグ
        'blank_line_after_opening_tag' => true,
        // 型厳密
        'declare_strict_types' => true,
        // namespace
        'blank_line_after_namespace' => true,
        'no_leading_namespace_whitespace' => true,
        // use
        'no_unused_imports' => true,
        'no_leading_import_slash' => true,
        // 配列
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => [
            'operators' => [
                '=>' => 'single_space',
            ],
        ],
        'no_whitespace_before_comma_in_array' => true,
        // 括弧
        'braces' => [
            'allow_single_line_closure' => false,
            'position_after_anonymous_constructs' => 'same',
            'position_after_control_structures' => 'same',
        ],
        'no_spaces_inside_parenthesis' => true,
        // 型宣言
        'compact_nullable_typehint' => true,
        // 改行
        'blank_line_before_statement' => [
            'statements' => [
                'break',
                'continue',
                'declare',
                'for',
                'foreach',
                'if',
                'return',
                'switch',
                // 'throw',
                'try',
            ]
        ],
        // キャスト
        'cast_spaces' => [
            'space' => 'none',
        ],
        'modernize_types_casting' => true,
        // isset, unset
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        // 結合演算子
        'concat_space' => [
            'spacing' => 'one',
        ],
        // __DIR__
        'dir_constant' => true,
        // 関数
        'function_declaration' => [
            'closure_function_spacing' => 'one',
        ],
        'function_typehint_space' => true,
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => false,
        ],
        'no_spaces_after_function_name' => true,
        // 小文字
        'lowercase_cast' => true,
        'lowercase_keywords' => true,
        'constant_case' => [
            'case' => 'lower',
        ],
        // new
        'new_with_braces' => true,
        // 空コメント
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_trailing_whitespace' => true,
        // return
        'no_useless_return' => true,
    ])
    ->setFinder($finder);
