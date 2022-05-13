<?php
declare(strict_types=1);

use Undkonsorten\CuteMailing\Domain\Model\MailTask;
use Undkonsorten\CuteMailing\Domain\Model\NewsletterTask;
use Undkonsorten\CuteMailing\Domain\Model\RecipientList;
use Undkonsorten\CuteMailing\Domain\Model\TtAddressRecipient;
use Undkonsorten\CuteMailing\Domain\Model\TtAddressRecipientList;
use Undkonsorten\CuteMailing\Domain\Model\RegisterAddressRecipient;
use Undkonsorten\Taskqueue\Domain\Model\Task;

return [
    MailTask::class => [
        'tableName' => 'tx_taskqueue_domain_model_task',
        'recordType' => MailTask::class,
        'properties' => [
            'newsletter' => [
                'fieldName' => 'tx_cutemailing_newsletter',
            ],
            'sendOut' => [
                'fieldName' => 'tx_cutemailing_sendout',
            ],
        ],
    ],
    NewsletterTask::class => [
        'tableName' => 'tx_taskqueue_domain_model_task',
        'recordType' => NewsletterTask::class,
        'properties' => [
            'newsletter' => [
                'fieldName' => 'tx_cutemailing_newsletter',
            ],
        ],
    ],
    Task::class => [
        'subclasses' => [
            MailTask::class => MailTask::class,
            NewsletterTask::class => NewsletterTask::class,
        ]
    ],
    TtAddressRecipientList::class => [
        'tableName' => 'tx_cutemailing_domain_model_recipientlist',
        'recordType' => TtAddressRecipientList::class
    ],
    RecipientList::class => [
        'tableName' => 'tx_cutemailing_domain_model_recipientlist',
        'subclasses' => [
            TtAddressRecipientList::class => TtAddressRecipientList::class,
        ]
    ],
    TtAddressRecipient::class => [
        'tableName' => 'tt_address',
    ],
    RegisterAddressRecipient::class => [
        'tableName' => 'tt_address',
    ],
];
