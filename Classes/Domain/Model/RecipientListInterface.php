<?php

namespace Undkonsorten\CuteMailing\Domain\Model;

interface RecipientListInterface
{
    /**
     * @return array
     */
    public function getRecipients(): array;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     * @return mixed
     */
    public function setName(string $name);


    /**
     * @param int $recipient
     * @return object
     */
    public function getRecipient(int $recipient): ?object;
}
