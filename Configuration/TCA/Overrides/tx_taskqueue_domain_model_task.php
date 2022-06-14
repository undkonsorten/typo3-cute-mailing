<?php

defined('TYPO3') or die();

(function() {
    $additionalColumns = [
        'tx_cutemailing_newsletter' => [
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.newsletter',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_cutemailing_domain_model_newsletter',
            ],
        ],
        'tx_cutemailing_sendout' => [
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_sendout',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_cutemailing_domain_model_sendout',
            ],
        ],
    ];
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_taskqueue_domain_model_task', $additionalColumns);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_taskqueue_domain_model_task', 'tx_cutemailing_newsletter, tx_cutemailing_sendout', '', 'after:retries');
})();
