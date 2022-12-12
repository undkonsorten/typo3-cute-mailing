<?php

namespace Undkonsorten\CuteMailing\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

abstract class RecipientList extends AbstractEntity implements RecipientListInterface
{
    /**
     * @var int
     */
    protected $recipientListPage;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

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


}
