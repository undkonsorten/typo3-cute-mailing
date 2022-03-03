<?php

use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

(function ($extKey = 'cute_mailing', $table = 'pages') {
    $cuteMailingSysFolder = 116;

    // Add new page type as possible select item:
    ExtensionManagementUtility::addTcaSelectItem(
        $table,
        'doktype',
        [
            'LLL:EXT:' . $extKey . '/Resources/Private/Language/locallang.xlf:cute_mailing_sys_folder',
            $cuteMailingSysFolder,
            'EXT:' . $extKey . '/Resources/Public/Icons/cute_folder_icon.svg'
        ],
        '1',
        'after'
    );

    ArrayUtility::mergeRecursiveWithOverrule(
        $GLOBALS['TCA'][$table],
        [
            // add icon for new page type:
            'ctrl' => [
                'typeicon_classes' => [
                    $cuteMailingSysFolder => 'apps-pagetree-cute-mailing',
                    $cuteMailingSysFolder . '-contentFromPid' => "apps-pagetree-cute-mailing",
                    $cuteMailingSysFolder . '-root' => "aapps-pagetree-cute-mailing",
                    $cuteMailingSysFolder . '-hideinmenu' => "apps-pagetree-cute-mailing",
                ],
            ],
            // add all page standard fields and tabs to your new page type
            'types' => [
                $cuteMailingSysFolder => [
                    'showitem' => $GLOBALS['TCA'][$table]['types'][PageRepository::DOKTYPE_DEFAULT]['showitem']
                ]
            ]
        ]
    );
})();
