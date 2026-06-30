<?php

namespace Undkonsorten\CuteMailing\Domain\Model;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use Undkonsorten\CuteMailing\Services\NewsletterService;
use Undkonsorten\Taskqueue\Domain\Model\Task;

class NewsletterTask extends Task
{

    /**
     * @var Newsletter
     */
    protected $newsletter;

    /**
     * @inheritDoc
     */
    public function run(): void
    {
        /** @var NewsletterService $newsletterService */
        $newsletterService = GeneralUtility::makeInstance(NewsletterService::class);
        $newsletterService->runNewsletter($this);

    }

    public function getTest(): bool
    {
        return (bool)$this->getProperty("test");
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

    public function setTest(bool $test): void
    {
        $this->setProperty("test", $test);
    }

    public function getAdditionalData(): array
    {
        return [
            'newsletter' => $this->newsletter,
        ];
    }

    public function setAttachImages(bool $attachImages): void
    {
        $this->setProperty('attachImages', $attachImages);
    }

    public function isAttachImages(): ?bool
    {
        return $this->getProperty('attachImages');
    }

}
