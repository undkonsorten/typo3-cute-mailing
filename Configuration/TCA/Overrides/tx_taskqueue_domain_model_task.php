<?php

defined('TYPO3') or die();

(function() {
    $additionalColumns = [
        'tx_cutemailing_newsletter' => [
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_taskqueue_domain_model_task.newsletter',
            'config' => [
                'type' => 'input',
                'eval' => 'int',
            ],
        ],
    ];
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_taskqueue_domain_model_task', $additionalColumns);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_taskqueue_domain_model_task', 'tx_cutemailing_newsletter', '', 'after:retries');
})();