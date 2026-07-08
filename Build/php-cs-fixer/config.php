<?php

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use TYPO3\CodingStandards\CsFixerConfig;

$config = CsFixerConfig::create();
$config->setParallelConfig(ParallelConfigFactory::detect());
$config->addRules([
    'native_function_invocation' => [
        'include' => [],
        'scope' => 'all',
        'strict' => true,
    ],
]);
$config->setCacheFile('.cache/.php-cs-fixer.cache');
$config->getFinder()->in('Classes')->in('Configuration')->in('Tests');

return $config;
