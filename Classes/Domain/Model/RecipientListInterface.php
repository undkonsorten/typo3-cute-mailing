<?php

namespace Undkonsorten\CuteMailing\Domain\Model;

interface RecipientListInterface
{
    /**
     * @return array
     */
    public function getRecipients(int $limit = null, int $offset = null): array;

    /**
     * @return int
     */
    public function getRecipientsCount(): int;

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
