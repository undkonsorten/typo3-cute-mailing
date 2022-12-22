<?php

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

(function ($extKey = 'cute_mailing', $table = 'pages') {

    // Add new page type as possible select item:
    ExtensionManagementUtility::addTcaSelectItem(
        $table,
        'module',
        [
            'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang.xlf:cute_mailing_sys_folder',
            $extKey,
            'EXT:' . $extKey . '/Resources/Public/Icons/cute_folder_icon.svg'
        ],
    );

    ArrayUtility::mergeRecursiveWithOverrule(
        $GLOBALS['TCA'][$table],
        [
            // add icon for new page type:
            'ctrl' => [
                'typeicon_classes' => [
                    'contains-'.$extKey => 'apps-pagetree-cute-mailing',
                ],
            ],
        ]
    );
})();
