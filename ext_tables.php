<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}
(function () {

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_cutemailing_domain_model_newsletter');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_cutemailing_domain_model_recipient_list');

    /**
     * Registers a Backend Module
     */
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Cutemailing',
        'web',     // Make module a submodule of 'tools'
        'cutemailing',    // Submodule key
        '',                        // Position
        [
            \Undkonsorten\CuteMailing\Controller\NewsletterController::class => 'list, new, edit, create, enable, disable, delete, receiver, sendTestMail, update',

        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:cute_mailing/Resources/Public/Icons/Cutemailing.svg',
            'labels' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_mod_newsletter.xlf',
        ]

    );
    (function ($extKey='cuteMailing') {
        $archiveDoktype = 116;

        // Add new page type:
        $GLOBALS['PAGES_TYPES'][$archiveDoktype] = [
            'type' => 'web',
            'allowedTables' => '*',
        ];

    })();
})();
