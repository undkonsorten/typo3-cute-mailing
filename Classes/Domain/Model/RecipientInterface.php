<?php

namespace Undkonsorten\CuteMailing\Domain\Model;


interface RecipientInterface
{

    /**
     * @return int
     */
    public function getUid(): int;

    /**
     * @return string
     */
    public function getEmail(): string;


    /**
     * @param string $email
     */
    public function setEmail(string $email): void;


    /**
     * @return string
     */
    public function getFirstName(): string;


    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void;

    /**
     * @return string
     */
    public function getLastName(): string;


    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void;


}
