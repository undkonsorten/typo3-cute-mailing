<?php

declare(strict_types=1);
if (!defined('TYPO3')) {
    die('Access denied.');
}

return [
    'ctrl' => [
        'title' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'versioningWS' => true,
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
        //@todo deprecated for TYPO3 14, can be removed after 13 support is dropped
        'searchFields' => 'name,data,status,start_date,message,priority,',
        'iconfile' => 'EXT:cute_mailing/Resources/Public/Icons/tx_cutemailing_domain_model_newsletter.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid,l10n_parent,l10n_diffsource,--palette--;;title, subject, description,sending_time, ' .
                ' newsletter_page, ' .
                '--div--;LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tabs.sender, --palette--;;sender, --palette--;;reply_to,' .
                '--div--;LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tabs.recipients, test_recipient_list,recipient_list,' .
                '--div--;LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tabs.technical_details,--palette--;;page_types,allowed_marker,return_path,language,basic_auth_user,basic_auth_password,listunsubscribe_enable,listunsubscribe_email,' .
                '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,hidden,starttime,endtime',
        ],
    ],
    'palettes' => [
        'sender' => [
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:palettes.sender',
            'showitem' => 'sender,sender_name',
        ],
        'reply_to' => [
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:palettes.reply_to',
            'showitem' => 'reply_to,reply_to_name',
        ],
        'page_types' => [
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:palettes.page_types',
            'showitem' => 'page_type_html,page_type_text',
        ],
        'title' => ['showitem' => 'title,status'],
    ],
    'columns' => [
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
                'searchable' => false,
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
                    'lower' => mktime(0, 0, 0, (int)date('m'), (int)date('d'), (int)date('Y')),
                ],
                'searchable' => false,
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
                    'lower' => mktime(0, 0, 0, (int)date('m'), (int)date('d'), (int)date('Y')),
                ],
                'searchable' => false,
            ],
        ],
        'newsletter_page' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.page',
            'config' => [
                'type' => 'group',
                'allowed' => 'pages',
                'size' => '1',
                'maxitems' => '1',
                'minitems' => '1',
            ],
        ],
        'send_outs' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.send_out',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_cutemailing_domain_model_sendout',
                'foreign_field' => 'newsletter',
            ],
        ],
        'recipient_list' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.recipients',
            'config' => [
                'type' => 'group',
                'allowed' => 'tx_cutemailing_domain_model_recipientlist',
                'size' => '1',
                'maxitems' => '1',
                'minitems' => '1',
            ],
        ],
        'test_recipient_list' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.test_recipients',
            'config' => [
                'type' => 'group',
                'allowed' => 'tx_cutemailing_domain_model_recipientlist',
                'size' => '1',
                'maxitems' => '1',
                'minitems' => '1',
            ],
        ],
        'sender' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.sender',
            'config' => [
                'type' => 'input',
                'size' => 15,
                'searchable' => false,
            ],
        ],
        'sender_name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.sender_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'searchable' => false,
            ],
        ],
        'reply_to' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.reply_to',
            'config' => [
                'type' => 'input',
                'size' => 15,
                'searchable' => false,
            ],
        ],
        'reply_to_name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.reply_to_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'searchable' => false,
            ],
        ],
        'title' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.title',
            'config' => [
                'type' => 'input',
                'size' => 25,
                'searchable' => false,
            ],
        ],
        'subject' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.subject',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'searchable' => false,
            ],
        ],
        'description' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.description',
            'config' => [
                'type' => 'text',
                'size' => 4,
                'searchable' => false,
            ],
        ],
        'status' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.status.0',
                        'value' => 0,
                    ],
                    [
                        'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.status.1',
                        'value' => 1,
                    ],
                    [
                        'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.status.2',
                        'value' => 2,
                    ],
                    [
                        'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.status.3',
                        'value' => 3,
                    ],
                ],
            ],
        ],
        'sending_time' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.sending_time',
            'config' => [
                'type' => 'datetime',
                'size' => 13,
                'checkbox' => 0,
                'default' => 0,
                'searchable' => false,
            ],
        ],
        'page_type_html' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.page_type_html',
            'config' => [
                'type' => 'number',
                'size' => 4,
            ],
        ],
        'page_type_text' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.page_type_text',
            'config' => [
                'type' => 'number',
                'size' => 4,
            ],
        ],
        'allowed_marker' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.allowed_marker',
            'config' => [
                'type' => 'text', 'searchable' => false,
            ],
        ],
        'return_path' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.return_path',
            'config' => [
                'type' => 'input', 'searchable' => false,
            ],
        ],
        'language' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.language',
            'config' => [
                'type' => 'number',
            ],
        ],
        'basic_auth_user' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.basic_auth_user',
            'config' => [
                'type' => 'input', 'searchable' => false,
            ],
        ],
        'basic_auth_password' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.basic_auth_password',
            'config' => [
                'type' => 'input', 'searchable' => false,
            ],
        ],

        'listunsubscribe_enable' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.listunsubscribe_enable',
            'config' => [
                'type' => 'input', 'searchable' => false,
            ],
        ],
        'listunsubscribe_email' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:cute_mailing/Resources/Private/Language/locallang_db.xlf:tx_cutemailing_domain_model_newsletter.listunsubscribe_email',
            'config' => [
                'type' => 'check',
            ],
        ],
    ],

];
