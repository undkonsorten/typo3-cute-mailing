<?php

namespace Undkonsorten\CuteMailing\Domain\Model;


use FriendsOfTYPO3\TtAddress\Domain\Model\Address;

class TtAddressRecipient extends Address implements RecipientInterface
{
    public function getUid(): int
    {
        return parent::getUid();
    }
}
