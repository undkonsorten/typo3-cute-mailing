<?php
declare(strict_types = 1);

return [
    \Undkonsorten\CuteMailing\Domain\Model\MailTask::class => [
        'tableName' => 'tx_taskqueue_domain_model_task',
        'recordType' => \Undkonsorten\CuteMailing\Domain\Model\MailTask::class
    ],
    \Undkonsorten\CuteMailing\Domain\Model\NewsletterTask::class => [
        'tableName' => 'tx_taskqueue_domain_model_task',
        'recordType' => \Undkonsorten\CuteMailing\Domain\Model\NewsletterTask::class
    ],
    \Undkonsorten\Taskqueue\Domain\Model\Task::class => [
        'subclasses' => [
            \Undkonsorten\CuteMailing\Domain\Model\MailTask::class => \Undkonsorten\CuteMailing\Domain\Model\MailTask::class,
            \Undkonsorten\CuteMailing\Domain\Model\NewsletterTask::class => \Undkonsorten\CuteMailing\Domain\Model\NewsletterTask::class,
        ]
    ],
    \Undkonsorten\CuteMailing\Domain\Model\TtAddressRecipientList::class => [
        'tableName' => 'tx_cutemailing_domain_model_recipientlist',
        'recordType' => \Undkonsorten\CuteMailing\Domain\Model\TtAddressRecipientList::class
    ],
    \Undkonsorten\CuteMailing\Domain\Model\RecipientList::class => [
        'tableName' => 'tx_cutemailing_domain_model_recipientlist',
        'subclasses' => [
            \Undkonsorten\CuteMailing\Domain\Model\TtAddressRecipientList::class => \Undkonsorten\CuteMailing\Domain\Model\TtAddressRecipientList::class,
        ]
    ],
];
