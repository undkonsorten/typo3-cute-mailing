<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend;

if (!defined('TYPO3')) {
    die('Access denied.');
}
call_user_func(
    function ($extKey = 'cute_mailing') {

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cute_mailing'] ??= [];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cute_mailing']['backend'] ??= Typo3DatabaseBackend::class;
    }

);

