<?php
declare(strict_types = 1);

return [
    \Undkonsorten\CuteMailing\Domain\Model\MailTask::class => [
        'tableName' => 'tx_taskqueue_domain_model_task',
        'recordType' => 'tx_cutemailing_domain_model_mail_task'
    ],
    \Undkonsorten\CuteMailing\Domain\Model\NewsletterTask::class => [
        'tableName' => 'tx_taskqueue_domain_model_task',
        'recordType' => 'tx_cutemailing_domain_model_newsletter_task'
    ],
    \Undkonsorten\Taskqueue\Domain\Model\Task::class => [
        'subclasses' => [
            'tx_cutemailing_domain_model_mail_task' => \Undkonsorten\CuteMailing\Domain\Model\MailTask::class,
            'tx_cutemailing_domain_model_newsletter_task' => \Undkonsorten\CuteMailing\Domain\Model\NewsletterTask::class,

        ]
    ],

];
