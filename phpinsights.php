<?php

declare(strict_types=1);

return [
	'preset' => 'laravel',
	'ide' => null,
	'exclude' => [],
	'add' => [],
	'remove' => [
		NunoMaduro\PhpInsights\Domain\Insights\ForbiddenTraits::class,
		PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff::class,
		PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowTabIndentSniff::class,
	],
	'config' => [
		PhpCsFixer\Fixer\Basic\BracesFixer::class => [
			'indent' => '	',
		],
	],
	'requirements' => [],
	'threads' => null,
];
