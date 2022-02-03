<?php

namespace Undkonsorten\CuteMailing\Services;

use FriendsOfTYPO3\TtAddress\Domain\Repository\AddressRepository;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use Undkonsorten\CuteMailing\Domain\Model\RecipientInterface;
use Undkonsorten\CuteMailing\Domain\Model\TtAddressRecipientList;

class RecipientService implements SingletonInterface
{
    public function createRecipients(TtAddressRecipientList $recipientList): ?array
    {
        $result = [];
        if(!is_null($recipientList->getRecipientListPage()) && $recipientList->getRecipientListType() == 'tt_address'){
            if(ExtensionManagementUtility::isLoaded('tt_address')){
                /**@var $addressRepository AddressRepository **/
                $addressRepository = GeneralUtility::makeInstance(AddressRepository::class);
                /**@var $defaultQuerySettings Typo3QuerySettings**/
                $defaultQuerySettings = $this->defaultQuerySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
                $defaultQuerySettings->setRespectStoragePage(true);
                $defaultQuerySettings->setStoragePageIds([$recipientList->getRecipientListPage()]);
                $addressRepository->setDefaultQuerySettings($defaultQuerySettings);
                $addresses = $addressRepository->findAll();
                foreach ($addresses as $address){
                    /** @var \FriendsOfTYPO3\TtAddress\Domain\Model\Address $address */
                    /** @var RecipientInterface $recipient */
                    $recipient = GeneralUtility::makeInstance(RecipientInterface::class);
                    $recipient->setEmail(new Address($address->getEmail()));
                    $recipient->setFirstName($address->getFirstName());
                    $recipient->setLastName($address->getLastName());
                    $result [] = $recipient;
                }
            }
        }
        return $result;
    }
}
