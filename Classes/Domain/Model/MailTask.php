<?php

namespace Undkonsorten\CuteMailing\Domain\Model;


use Undkonsorten\CuteMailing\Services\MailService;
use Undkonsorten\Taskqueue\Domain\Model\Task;
use TYPO3\CMS\Extbase\Annotation as Extbase;

class MailTask extends Task
{

    const PLAINTEXT = 'plain';
    const HTML = 'html';
    const BOTH = 'both';

    /**
     * @var Newsletter
     */
    protected $newsletter = null;

    /**
     * @var SendOut
     */
    protected $sendOut;

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
        $this->sendOut->incrementCompleted();
        $this->getNewsletter() && $this->getNewsletter()->updateStatus();
    }


    public function setFormat(string $format): void
    {
        $this->setProperty("format", $format);
    }

    public function getFormat(): string
    {
        return $this->getProperty("format");
    }

    public function getNewsletter(): ?Newsletter
    {
        return $this->newsletter;
    }

    public function setNewsletter(?Newsletter $newsletter): self
    {
        $this->newsletter = $newsletter;
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

    public function setSendOut(SendOut $sendOut): self
    {
        $this->sendOut = $sendOut;
        return $this;
    }

    public function getSendOut(): SendOut
    {
        return $this->sendOut;
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
