<?php

namespace Undkonsorten\CuteMailing\Domain\Model;

use DateTime;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Newsletter extends AbstractEntity
{
    const CREATED = 0;
    const TESTED = 1;
    const SCHEDULED = 2;
    const SENT = 3;

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var DateTime
     */
    protected $sendingTime = null;

    /**
     * @var string
     */
    protected $description = '';
    /**
     * @var int
     */
    protected $newsletterPage = 0;

    /**
     * @var RecipientList
     */
    protected $recipientList = null;

    /**
     * @var RecipientList
     */
    protected $testRecipientList = null;

    /**
     * @var string
     */
    protected $sender = '';

    /**
     * @var string
     */
    protected $senderName = '';

    /**
     * @var string
     */
    protected $replyTo = '';

    /**
     * @var string
     */
    protected $replyToName = '';

    /**
     * @var string
     */
    protected $subject = '';

    /**
     * @var int
     */
    protected $status = self::CREATED;

    /**
     * @var int|null
     */
    protected $pageTypeHtml = null;

    /**
     * @var int|null
     */
    protected $pageTypeText = null;

    /**
     * @var string|null
     */
    protected $allowedMarker = null;


    /**
     * @return int
     */
    public function getNewsletterPage(): int
    {
        return $this->newsletterPage;
    }

    /**
     * @param int $newsletterPage
     */
    public function setNewsletterPage(int $newsletterPage): void
    {
        $this->newsletterPage = $newsletterPage;
    }

    /**
     * @return RecipientList
     */
    public function getRecipientList(): RecipientList
    {
        return $this->recipientList;
    }

    /**
     * @param RecipientList $recipientList
     */
    public function setRecipientList(RecipientList $recipientList): void
    {
        $this->recipientList = $recipientList;
    }

    /**
     * @return mixed
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param mixed $configuration
     */
    public function setConfiguration($configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return DateTime
     */
    public function getSendingTime(): DateTime
    {
        return $this->sendingTime ?: new DateTime();
    }

    /**
     * @param DateTime $sendingTime
     */
    public function setSendingTime(?DateTime $sendingTime): void
    {
        $this->sendingTime = $sendingTime;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getSender(): string
    {
        return $this->sender;
    }

    /**
     * @param string $sender
     */
    public function setSender(string $sender): void
    {
        $this->sender = $sender;
    }

    /**
     * @return string
     */
    public function getSenderName(): string
    {
        return $this->senderName;
    }

    /**
     * @param string $senderName
     */
    public function setSenderName(string $senderName): void
    {
        $this->senderName = $senderName;
    }

    /**
     * @return string
     */
    public function getReplyTo(): string
    {
        return $this->replyTo;
    }

    /**
     * @param string $replyTo
     */
    public function setReplyTo(string $replyTo): void
    {
        $this->replyTo = $replyTo;
    }

    /**
     * @return string
     */
    public function getReplyToName(): string
    {
        return $this->replyToName;
    }

    /**
     * @param string $replyToName
     */
    public function setReplyToName(string $replyToName): void
    {
        $this->replyToName = $replyToName;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return RecipientList
     */
    public function getTestRecipientList(): ?RecipientList
    {
        return $this->testRecipientList;
    }

    /**
     * @param RecipientList $testRecipientList
     */
    public function setTestRecipientList(?RecipientList $testRecipientList): void
    {
        $this->testRecipientList = $testRecipientList;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function enable(): void
    {
        $this->setStatus(self::SCHEDULED);
    }

    public function markSent(): self
    {
        $this->setStatus(self::SENT);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPageTypeHtml(): ?int
    {
        return $this->pageTypeHtml;
    }

    /**
     * @param int|null $pageTypeHtml
     */
    public function setPageTypeHtml(?int $pageTypeHtml): void
    {
        $this->pageTypeHtml = $pageTypeHtml;
    }

    /**
     * @return int|null
     */
    public function getPageTypeText(): ?int
    {
        return $this->pageTypeText;
    }

    /**
     * @param int|null $pageTypeText
     */
    public function setPageTypeText(?int $pageTypeText): void
    {
        $this->pageTypeText = $pageTypeText;
    }

    /**
     * @return string|null
     */
    public function getAllowedMarker(): ?string
    {
        return $this->allowedMarker;
    }

    /**
     * @param string|null $allowedMarker
     */
    public function setAllowedMarker(?string $allowedMarker): void
    {
        $this->allowedMarker = $allowedMarker;
    }


}
