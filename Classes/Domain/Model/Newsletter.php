<?php

namespace Undkonsorten\CuteMailing\Domain\Model;

use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Extbase\Annotation\ORM\Cascade;
use DateTime;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

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
     * @var string
     */
    protected $newsletterPageUrl = '';

    /**
     * @var RecipientList
     * @Lazy
     */
    protected $recipientList = null;

    /**
     * @var RecipientList
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
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
     * @var string|null
     */
    protected $returnPath = null;

    /**
     * @var int
     */
    protected $language = 0;

    /**
     * @var string|null
     */
    protected $basicAuthUser = null;

    /**
     * @var string|null
     */
    protected $basicAuthPassword = null;


    /**
     * @var bool
     */
    protected $listunsubscribeEnable = null;

    /**
     * @var string|null
     */
    protected $listunsubscribeEmail = null;


    /**
     * @var ObjectStorage<SendOut>
     * @Cascade("remove")
     * @Lazy
     */
    protected $sendOuts;

    public function __construct()
    {
        $this->sendOuts = new ObjectStorage();
    }

    /**
     * @return string|null
     */
    public function getBasicAuthUser(): ?string
    {
        return $this->basicAuthUser;
    }

    /**
     * @param string|null $basicAuthUser
     */
    public function setBasicAuthUser(?string $basicAuthUser): void
    {
        $this->basicAuthUser = $basicAuthUser;
    }

    /**
     * @return string|null
     */
    public function getBasicAuthPassword(): ?string
    {
        return $this->basicAuthPassword;
    }

    /**
     * @param string|null $basicAuthPassword
     */
    public function setBasicAuthPassword(?string $basicAuthPassword): void
    {
        $this->basicAuthPassword = $basicAuthPassword;
    }

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
        if ($this->recipientList instanceof LazyLoadingProxy) {
            $this->recipientList->_loadRealInstance();
        }
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
        if ($this->testRecipientList instanceof LazyLoadingProxy) {
            $this->testRecipientList->_loadRealInstance();
        }
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

    public function isScheduled(): bool
    {
        return $this->status === self::SCHEDULED;
    }

    public function isSent(): bool
    {
        return $this->status === self::SENT;
    }

    public function setSendOuts(ObjectStorage $sendOuts): self
    {
        $this->sendOuts = $sendOuts;
        return $this;
    }

    public function getSendOuts(): ObjectStorage
    {
        if ($this->sendOuts instanceof LazyLoadingProxy) {
            $this->sendOuts->_loadRealInstance();
        }
        return $this->sendOuts;
    }

    public function getLatestSendOut(): ?SendOut
    {
        $sendOuts = $this->sendOuts->toArray() ?? [];
        return array_pop($sendOuts);
    }

    public function getLatestNonTestSendout(): ?SendOut
    {
        $sendOuts = $this->sendOuts->toArray() ?? [];
        $sendOuts = array_filter($sendOuts, static function(SendOut $sendout): bool {
            return !$sendout->isTest();
        });
        return array_pop($sendOuts);
    }

    public function addSendOut(SendOut $sendOut): self
    {
        $this->sendOuts->attach($sendOut);
        return $this;
    }

    public function getProgress(): float
    {
        return (float)($this->getLatestNonTestSendout() ? $this->getLatestNonTestSendout()->getProgress() : 0);
    }

    public function isComplete(): bool
    {
        return $this->getLatestNonTestSendout() && $this->getLatestNonTestSendout()->isComplete();
    }

    public function updateStatus(): self
    {
        if ($this->getStatus() === Newsletter::SCHEDULED && $this->isComplete()) {
            $this->setStatus(Newsletter::SENT);
        }
        return $this;
    }

    public function setReturnPath(?string $returnPath): self
    {
        $this->returnPath = $returnPath;
        return $this;
    }

    public function getReturnPath(): ?string
    {
        return $this->returnPath;
    }

    public function setLanguage(int $language): self
    {
        $this->language = $language;
        return $this;
    }

    public function getLanguage(): int
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getNewsletterPageUrl(): string
    {
        return $this->newsletterPageUrl;
    }

    /**
     * @param string $newsletterPageUrl
     */
    public function setNewsletterPageUrl(string $newsletterPageUrl): void
    {
        $this->newsletterPageUrl = $newsletterPageUrl;
    }

    /**
     * @return string|null
     */
    public function getListunsubscribeEmail(): ?string
    {
        return $this->listunsubscribeEmail;
    }

    /**
     * @param string|null $listunsubscribeEmail
     */
    public function setListunsubscribeEmail(?string $listunsubscribeEmail): void
    {
        $this->listunsubscribeEmail = $listunsubscribeEmail;
    }

    /**
     * @return bool|null
     */
    public function getListunsubscribeEnable(): ?bool
    {
        return $this->listunsubscribeEnable;
    }

    /**
     * @param bool|null $listunsubscribeEnable
     */
    public function setListunsubscribeEnable(?bool $listunsubscribeEnable): void
    {
        $this->listunsubscribeEnable = $listunsubscribeEnable;
    }


}
