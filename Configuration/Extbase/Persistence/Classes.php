<?php
declare(strict_types=1);

use Undkonsorten\CuteMailing\Domain\Model\LineSeparatedRecipientList;
use Undkonsorten\CuteMailing\Domain\Model\MailTask;
use Undkonsorten\CuteMailing\Domain\Model\NewsletterTask;
use Undkonsorten\CuteMailing\Domain\Model\RecipientList;
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
    LineSeparatedRecipientList::class => [
        'tableName' => 'tx_cutemailing_domain_model_recipientlist',
        'recordType' => LineSeparatedRecipientList::class
    ],
    RecipientList::class => [
        'tableName' => 'tx_cutemailing_domain_model_recipientlist',
        'subclasses' => [
            LineSeparatedRecipientList::class => LineSeparatedRecipientList::class
        ],
    ],
];
