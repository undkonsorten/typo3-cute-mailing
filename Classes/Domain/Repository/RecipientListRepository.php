<?php

namespace Undkonsorten\CuteMailing\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

class RecipientListRepository extends Repository implements RecipientListRepositoryInterface
{
    /**
     * @return array|object[]|QueryResultInterface
     */
    public function findByRootline(array $rootline)
    {
        $storagePageIds = [];
        foreach ($rootline as $key => $value) {
            $storagePageIds[] = $value['uid'];
        }
        $query = $this->createQuery();
        $defaultSettings = $query->getQuerySettings();
        $defaultSettings->setRespectStoragePage(true);
        $defaultSettings->setStoragePageIds($storagePageIds);
        $query->setQuerySettings($defaultSettings);
        return $query->execute();
    }
}
