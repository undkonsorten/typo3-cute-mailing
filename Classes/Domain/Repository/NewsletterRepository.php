<?php
namespace Undkonsorten\CuteMailing\Domain\Repository;

use TYPO3\CMS\Backend\Search\LiveSearch\QueryParser;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser;
use TYPO3\CMS\Extbase\Persistence\Repository;

class NewsletterRepository extends Repository
{

    /**
     * @return array|object[]|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByRootline(array $rootline)
    {
        $storagePageIds = [];
        foreach ($rootline as $key => $value){
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
