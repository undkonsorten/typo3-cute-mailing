<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}
call_user_func(
    function ($extKey='cute_mailing') {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptConstants('@import "EXT:cute_mailing/Configuration/TypoScript/constants.typoscript"');
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('@import "EXT:cute_mailing/Configuration/TypoScript/setup.typoscript"');
        $cuteMailingSysFolder = 116;

        // Provide icon for page tree, list view, ... :
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry
            ->registerIcon(
                'apps-pagetree-archive',
                TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                [
                    'source' => 'EXT:' . $extKey . '/Resources/Public/Icons/Extension.svg',
                ]
            );
        $iconRegistry
            ->registerIcon(
                'apps-pagetree-archive-contentFromPid',
                TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                [
                    'source' => 'EXT:' . $extKey . '/Resources/Public/Icons/Extension.svg',
                ]
            );
        // ... register other icons in the same way, see below.

        // Allow backend users to drag and drop the new page type:
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig(
            'options.pageTree.doktypesToShowInNewPageDragArea := addToList(' . $cuteMailingSysFolder . ')'
        );

    }
);

