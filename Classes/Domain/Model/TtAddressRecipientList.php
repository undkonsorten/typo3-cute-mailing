<?php

namespace Undkonsorten\CuteMailing\Domain\Model;

use FriendsOfTYPO3\TtAddress\Domain\Model\Address;
use FriendsOfTYPO3\TtAddress\Domain\Repository\AddressRepository;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use Undkonsorten\CuteMailing\Domain\Repository\TtAddressRecipientRepository;

class TtAddressRecipientList extends RecipientList
{
    /**
     * @var int
     */
    protected $recipientListPage;

    public function getRecipients(): array
    {
        $result = [];
        if (ExtensionManagementUtility::isLoaded('tt_address')) {
            /**@var $addressRepository AddressRepository * */
            $addressRepository = GeneralUtility::makeInstance(TtAddressRecipientRepository::class);
            /**@var $defaultQuerySettings Typo3QuerySettings* */
            $defaultQuerySettings = $this->defaultQuerySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
            $defaultQuerySettings->setRespectStoragePage(true);
            $defaultQuerySettings->setStoragePageIds([$this->getRecipientListPage()]);
            $addressRepository->setDefaultQuerySettings($defaultQuerySettings);
            $result = $addressRepository->findAll()->toArray();
        }
        return $result;
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

    public function getRecipient(int $recipient): ?Address
    {
        $result = null;
        if (ExtensionManagementUtility::isLoaded('tt_address')) {
            /**@var $addressRepository AddressRepository * */
            $addressRepository = GeneralUtility::makeInstance(TtAddressRecipientRepository::class);
            /**@var $defaultQuerySettings Typo3QuerySettings* */
            $defaultQuerySettings = $this->defaultQuerySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
            $defaultQuerySettings->setRespectStoragePage(true);
            $defaultQuerySettings->setStoragePageIds([$this->getRecipientListPage()]);
            $addressRepository->setDefaultQuerySettings($defaultQuerySettings);
            $result = $addressRepository->findByUid($recipient);
        }
        return $result;
    }
}
