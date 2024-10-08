<?php

use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}
call_user_func(
    function ($extKey = 'cute_mailing') {
        $typo3VersionInformation = GeneralUtility::makeInstance(Typo3Version::class);

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['cuteMailing_folderUpdateWizard']
            = \Undkonsorten\CuteMailing\Updates\FolderUpdateWizard::class;

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cute_mailing'] ??= [];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cute_mailing']['backend'] ??= \TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend::class;

        if ( $typo3VersionInformation->getMajorVersion() < 13) {
            $GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] = $GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'].', module';
        }
    }

);

