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
})();
