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

    public function findAll(int $limit = null, int $offset = null)
    {
        $query = $this->createQuery();
        if(!is_null($limit) && $limit > 0){
            $query->setLimit($limit);
        }
        if(!is_null($offset) && $offset >0){
            $query->setOffset($offset);
        }
        return $query->execute();
    }
}
