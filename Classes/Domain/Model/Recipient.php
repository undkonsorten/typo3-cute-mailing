<?php

namespace Undkonsorten\CuteMailing\Domain\Model;

use Symfony\Component\Mime\Address;

class Recipient
{
    /**
     * @var Address
     * @Extbase\Validate("notEmpty")
     */
    protected $email = '';

    protected $firstName = '';

    protected $lastName = '';

    protected $properties = [];

    /**
     * @return string
     */
    public function getEmail(): Address
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(Address $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return array
     */
    public function getProperty(string $property): ?object
    {
        return $this->properties[$property] ?? null;

    }

    /**
     * @param array $property
     */
    public function setProperty(string $property,  $value): void
    {
        $this->properties[$property] = $value;
    }



}
