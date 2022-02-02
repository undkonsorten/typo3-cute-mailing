<?php

namespace Undkonsorten\CuteMailing\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class RecipientList extends AbstractEntity
{
    /**
     * @var int
     */
    protected $recipientListPage;

    /**
     * @var string
     */
    protected $recipientListType;

    /**
     * @return int
     */
    public function getRecipientListPage(): int
    {
        return $this->recipientListPage;
    }

    /**
     * @param int $recipientListPage
     */
    public function setRecipientListPage(int $recipientListPage): void
    {
        $this->recipientListPage = $recipientListPage;
    }

    /**
     * @return string
     */
    public function getRecipientListType(): string
    {
        return $this->recipientListType;
    }

    /**
     * @param string $recipientListType
     */
    public function setRecipientListType(string $recipientListType): void
    {
        $this->recipientListType = $recipientListType;
    }



}
