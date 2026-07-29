<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Domain\Model;

interface RecipientListInterface
{
    /**
     * @return array<RecipientInterface>
     */
    public function getRecipients(?int $limit = null, ?int $offset = null): array;

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
     */
    public function removeRecipientByEmail(string $email): void;

    /**
     * @param int $recipient
     */
    public function removeRecipientById(int $recipient): void;

    /**
     * @param string $email
     */
    public function disableRecipientByEmail(string $email): void;

    /**
     * @param int $recipient
     */
    public function disableRecipientById(int $recipient): void;

}
