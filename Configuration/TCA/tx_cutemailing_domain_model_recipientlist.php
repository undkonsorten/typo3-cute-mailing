<?php
declare(strict_types=1);

use Undkonsorten\CuteMailing\Domain\Model\LineSeparatedRecipientList;

if (!defined('TYPO3')) {
    die('Access denied.');
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_recipient_list',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'delete' => 'deleted',
        'type' => 'record_type',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'name,data,status,start_date,message,priority,',
        'iconfile' => 'EXT:cute_mailing/Resources/Public/Icons/tx_cutemailing_domain_model_recipient_list.svg'
    ],
    'types' => [
        '0' => ['showitem' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,--palette--;;1,name,record_type,line_separated_list,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,starttime,endtime'],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ]
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
        'name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_recipient_list.name',
            'config' => [
                'type' => 'input',
                'size' => 10,
            ]
        ],
        'recipient_list_page' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_recipient_list.page',
            'config' => [
                'type' => 'group',
                'allowed' => 'pages',
                'size' => '1',
                'maxitems' => '1',
                'minitems' => '0',
            ]
        ],
        'record_type' => [
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_recipient_list.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_recipient_list.type.line_separated_list',
                        'value' => LineSeparatedRecipientList::class
                    ]
                ],
            ],
        ],
        'line_separated_list' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_recipient_list.line_separated_list',
            'description' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_recipient_list.line_separated_list.description',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 10,
            ]
        ],
    ],
];
