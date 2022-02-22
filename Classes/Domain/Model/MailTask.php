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

    public function getNewsletter(): int
    {
        return (int)$this->getProperty('newsletter');
    }

    public function setNewsletter(int $newsletter): void
    {
        $this->setProperty('newsletter', $newsletter);
    }

    public function getRecipient(): int
    {
        return (int)$this->getProperty('recipient');
    }

    public function setRecipient(int $recipient): void
    {
        $this->setProperty('recipient', $recipient);
    }

    public function getPageTypeHtml(): int
    {
        return (int)$this->getProperty('pageTypeHtml');
    }

    public function getPageTypeText(): int
    {
        return (int)$this->getProperty('pageTypeText');
    }

    public function setPageTypeText(int $value): void
    {
        $this->setProperty('pageTypeText', $value);
    }

    public function setPageTypeHtml(int $value): void
    {
        $this->setProperty('pageTypeHtml', $value);
    }


}
