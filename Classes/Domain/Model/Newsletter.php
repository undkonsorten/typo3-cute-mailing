<?php

namespace Undkonsorten\CuteMailing\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use Undkonsorten\CuteMailing\Domain\Repository\RecipientListRepository;

class Newsletter extends AbstractEntity
{
    /**
     * @var int
     */
    protected $newsletterPage = 0;

    /**
     * @var RecipientList
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




}
