<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use Undkonsorten\CuteMailing\Controller\NewsletterController;

if (!defined('TYPO3')) {
    die('Access denied.');
}
(function () {

    /**
     * Registers a Backend Module
     */
    ExtensionUtility::registerModule(
        'CuteMailing',
        'web',     // Make module a submodule of 'tools'
        'cutemailing',    // Submodule key
        '',                        // Position
        [
            NewsletterController::class => 'list, new, edit, create, enable, delete, receiver, sendTestMail, update',

        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:cute_mailing/Resources/Public/Icons/CuteMailing.svg',
            'labels' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_mod_newsletter.xlf',
        ]

    );
    (function ($extKey = 'cuteMailing') {
        $cuteMailingDoktype = 116;

        // Add new page type:
        \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\DataHandling\PageDoktypeRegistry::class)->add($cuteMailingDoktype, [
            'type' => 'web',
            'allowedTables' => '*',
        ]);

    })();
})();
