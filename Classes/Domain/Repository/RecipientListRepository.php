<?php

namespace Undkonsorten\CuteMailing\Domain\Repository;

use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use Undkonsorten\CuteMailing\Domain\Model\RecipientList;

class RecipientListRepository extends Repository implements RecipientListRepositoryInterface
{
    /**
     * @return array|object[]|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAll()
    {
        $query = $this->createQuery();
        $defaultSettings = $query->getQuerySettings();
        $defaultSettings->setRespectStoragePage(false);
        $query->setQuerySettings($defaultSettings);
        return $query->execute();
    }
}
