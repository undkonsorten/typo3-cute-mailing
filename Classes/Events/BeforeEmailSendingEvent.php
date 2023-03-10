<?php

namespace Undkonsorten\CuteMailing\Events;

use TYPO3\CMS\Core\Mail\MailMessage;

final class BeforeEmailSendingEvent
{
    /**
     * @var MailMessage
     */
    private MailMessage $email;

    public function __construct(MailMessage $email)
    {
        $this->email = $email;
    }

    /**
     * @return MailMessage
     */
    public function getEmail(): MailMessage
    {
        return $this->email;
    }

    /**
     * @param MailMessage $email
     * @return BeforeEmailSendingEvent
     */
    public function setEmail(MailMessage $email): void
    {
        $this->email = $email;
    }



}