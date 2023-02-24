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

    public function getNewsletter(): ?int
    {
        return $this->getProperty('newsletter');
    }

    public function setNewsletter(?int $newsletter): self
    {
        $this->setProperty('newsletter', $newsletter);
        return $this;
    }

    public function getRecipient(): int
    {
        return (int)$this->getProperty('recipient');
    }

    public function setRecipient(int $recipient): void
    {
        $this->setProperty('recipient', $recipient);
    }

    public function getAdditionalData(): array
    {
        return [
            'newsletter' => $this->getNewsletter(),
            'sendOut' => $this->getSendOut(),
        ];
    }

    public function setSendOut(int $sendOut): self
    {
        $this->setProperty('sendOut', $sendOut);
        return $this;
    }

    public function getSendOut(): ?int
    {
        return $this->getProperty('sendOut');
    }

    public function isAttachImages(): ?bool
    {
        return $this->getProperty('attachImages');
    }

    public function setAttachImages(bool $attachImages): self
    {
        $this->setProperty('attachImages', $attachImages);
        return $this;
    }

}
