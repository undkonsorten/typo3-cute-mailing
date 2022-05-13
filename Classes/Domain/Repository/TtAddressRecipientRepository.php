<?php

namespace Undkonsorten\CuteMailing\Domain\Repository;

use FriendsOfTYPO3\TtAddress\Domain\Repository\AddressRepository;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use Undkonsorten\CuteMailing\Domain\Model\TtAddressRecipient;
use Undkonsorten\CuteMailing\Domain\Model\RegisterAddressRecipient;

class TtAddressRecipientRepository extends AddressRepository
{

    protected $objectType = TtAddressRecipient::class;

    public function initializeObject()
    {
        if (ExtensionManagementUtility::isLoaded('registeraddress')) {
            $this->objectType = RegisterAddressRecipient::class;
        }
    }

}
