<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}
(function () {

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
        'tx_cutemailing_domain_model_newsletter',
        'EXT:cute_mailing/Resources/Private/Language/locallang_csh_tx_cutemailing_domain_model_newsletter.xlf'
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
        'tx_cutemailing_domain_model_recipient_list',
        'EXT:cute_mailing/Resources/Private/Language/locallang_csh_tx_cutemailing_domain_model_recipient_list.xlf'
    );
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
            \Undkonsorten\CuteMailing\Controller\NewsletterController::class => 'list, new, create, enable, disable, delete, receiver',

        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:cute_mailing/Resources/Public/Icons/lux_module_newsletter.svg',
            'labels' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_mod_newsletter.xlf',
        ]

    );
})();
