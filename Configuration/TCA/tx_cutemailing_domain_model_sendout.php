<?php
declare(strict_types=1);
if (!defined('TYPO3')) {
    die('Access denied.');
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_sendout',
        'label' => 'newsletter',
        'label_alt' => 'crdate',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'newsletter',
        'iconfile' => 'EXT:cute_mailing/Resources/Public/Icons/tx_cutemailing_domain_model_sendout.svg'
    ],
    'types' => [
        '1' => ['showitem' => 'hidden, newsletter, mail_tasks, total, test'],
    ],
    'columns' => [
        'crdate' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.creationDate',
            'config' => [
                'type' => 'datetime',
                'default' => 0,
            ],
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'datetime',
                'size' => 13,
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, (int)date('m'), (int)date('d'), (int)date('Y'))
                ],
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'datetime',
                'size' => 13,
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, (int)date('m'), (int)date('d'), (int)date('Y'))
                ],
            ],
        ],
        'newsletter' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_sendout.newsletter',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_cutemailing_domain_model_newsletter',
                'maxitems' => '1',
                'minitems' => '1',
            ],
        ],
        'mail_tasks' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_sendout.mail_tasks',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_taskqueue_domain_model_task',
                'foreign_field' => 'tx_cutemailing_sendout',
            ],
        ],
        'total' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_sendout.total',
            'config' => [
                'type' => 'number',
                'size' => 4,
            ],
        ],
        'completed' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_sendout.completed',
            'config' => [
                'type' => 'number',
                'size' => 4,
            ],
        ],
        'test' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_sendout.test',
            'config' => [
                'type' => 'check',
                'readOnly' => true,
            ],
        ],
    ],
];
