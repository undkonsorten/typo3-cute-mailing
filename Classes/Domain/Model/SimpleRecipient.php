<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Domain\Model;

class SimpleRecipient implements RecipientInterface
{

    protected $uid = 0;

    /**
     * @var string
     */
    protected $email = '';

    /**
     * @var string
     */
    protected $firstName = '';

    /**
     * @var string
     */
    protected $lastName = '';

    /**
     * @inheritDoc
     */
    public function getUid(): int
    {
        return $this->uid;
    }

    public function setUid(int $uid): self
    {
        $this->uid = $uid;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @inheritDoc
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @inheritDoc
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @inheritDoc
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @inheritDoc
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @inheritDoc
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }
}
