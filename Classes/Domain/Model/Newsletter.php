<?php

namespace Undkonsorten\CuteMailing\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class Newsletter extends AbstractEntity
{
    /**
     * @var int
     */
    protected $newsletterPage = 0;

    /**
     * @var AbstractRecipientList
     */
    protected $recipientList;

    /**
     * @var string
     */
    protected $configuration;

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
     * @return AbstractRecipientList
     */
    public function getRecipientList(): AbstractRecipientList
    {
        return $this->recipientList;
    }

    /**
     * @param AbstractRecipientList $recipientList
     */
    public function setRecipientList(AbstractRecipientList $recipientList): void
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




}
