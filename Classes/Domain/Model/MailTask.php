<?php

namespace Undkonsorten\CuteMailing\Domain\Model;


use Undkonsorten\CuteMailing\Services\MailService;
use Undkonsorten\Taskqueue\Domain\Model\Task;

class MailTask extends Task
{

    const PLAINTEXT = 'plain';
    const HTML = 'html';
    const BOTH = 'both';

    /**
     * @var int
     */
    protected $newsletter;

    /**
     * @var MailService
     */
    protected $mailService;

    public function injectMailService(MailService $mailService)
    {
        $this->mailService = $mailService;
    }


    public function run(): void
    {
        $this->mailService->sendMail($this);
    }


    public function setFormat(string $format): void
    {
        $this->setProperty("format", $format);
    }

    public function getFormat(): string
    {
        return $this->getProperty("format");
    }

    public function getNewsletter(): ?int
    {
        return $this->newsletter;
    }

    public function setNewsletter(?int $newsletter): void
    {
        $this->newsletter = $newsletter;
    }

    public function getRecipient(): int
    {
        return (int)$this->getProperty('recipient');
    }

    public function setRecipient(int $recipient): void
    {
        $this->setProperty('recipient', $recipient);
    }


}
