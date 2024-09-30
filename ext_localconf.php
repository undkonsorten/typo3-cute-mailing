<?php

use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}
call_user_func(
    function ($extKey = 'cute_mailing') {
        ExtensionManagementUtility::addTypoScriptConstants('@import "EXT:cute_mailing/Configuration/TypoScript/constants.typoscript"');
        ExtensionManagementUtility::addTypoScriptSetup('@import "EXT:cute_mailing/Configuration/TypoScript/setup.typoscript"');

        // Provide icon for page tree, list view, ... :
        $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
        $iconRegistry
            ->registerIcon(
                'apps-pagetree-cute-mailing',
                TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                [
                    'source' => 'EXT:' . $extKey . '/Resources/Public/Icons/cute_folder_icon.svg',
                ]
            );

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['cuteMailing_folderUpdateWizard']
            = \Undkonsorten\CuteMailing\Updates\FolderUpdateWizard::class;

        $GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] = $GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'].', module';

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cute_mailing']
            ??= [];
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cute_mailing']['backend']
            ??= \TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend::class;
    }

);

