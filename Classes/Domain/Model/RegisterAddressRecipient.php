<?php

namespace Undkonsorten\CuteMailing\Domain\Model;


use FriendsOfTYPO3\TtAddress\Domain\Model\Address;

class RegisterAddressRecipient extends Address implements RecipientInterface
{
    /**
     * @var string
     */
    protected $registeraddresshash;

    public function getUid(): int
    {
        return parent::getUid();
    }

    public function getRegisteraddresshash(): ?string
    {
        return $this->registeraddresshash;
    }
}
