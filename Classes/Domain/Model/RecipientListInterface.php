<?php

namespace Undkonsorten\CuteMailing\Domain\Model;

interface RecipientListInterface
{
    /**
     * @return array<RecipientInterface>
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
     * @return RecipientInterface|null
     */
    public function getRecipient(int $recipient): ?RecipientInterface;

    /**
     * @param string $email
     * @return void
     */
    public function removeRecipientByEmail(string $email): void;

    /**
     * @param int $recipient
     * @return void
     */
    public function removeRecipientById(int $recipient): void;

    /**
     * @param string $email
     * @return void
     */
    public function disableRecipientByEmail(string $email): void;

    /**
     * @param int $recipient
     * @return void
     */
    public function disableRecipientById(int $recipient): void;


}
