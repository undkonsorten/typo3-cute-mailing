<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Domain\Model;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LineSeparatedRecipientList extends RecipientList
{

    /**
     * @var string
     */
    protected $lineSeparatedList;

    /**
     * @inheritDoc
     */
    public function getRecipients(int $limit = null, int $offset = null): array
    {
        // @todo validate email addresses?
        // @todo allow format "FirstName LastName <firstname.lastname@example.org>"?
        $addresses = GeneralUtility::trimExplode("\n", $this->lineSeparatedList, true);
        return array_map(function (string $address, int $position): SimpleRecipient {
            $recipient = new SimpleRecipient();
            $recipient->setUid($position)->setEmail($address);
            return $recipient;
        }, $addresses, array_keys($addresses));
    }

    public function getRecipientsCount(): int
    {
        $addresses = GeneralUtility::trimExplode("\n", $this->lineSeparatedList, true);
        return count($addresses);
    }

    /**
     * @inheritDoc
     */
    public function getRecipient(int $recipient): ?SimpleRecipient
    {
        $filteredRecipients = array_filter($this->getRecipients(), function (SimpleRecipient $recipientObject) use ($recipient) {
            return $recipientObject->getUid() === $recipient;
        });
        return array_values($filteredRecipients)[0] ?? null;
    }

    /**
     * @param int $recipient
     * @return void
     * @throws \Exception
     */
    public function removeRecipientById(int $recipient): void
    {
        throw new \Exception("Not implemented.", 1700484658);
    }

    /**
     * @param string $email
     * @return void
     * @throws \Exception
     */
    public function removeRecipientByEmail(string $email): void
    {
        throw new \Exception("Not implemented.", 1700484658);
    }

    /**
     * @param string $email
     * @return void
     * @throws \Exception
     */
    public function disableRecipientByEmail(string $email): void
    {
        throw new \Exception("Not implemented.", 1700484664);
    }

    /**
     * @param int $recipient
     * @return void
     * @throws \Exception
     */
    public function disableRecipientById(int $recipient): void
    {
        throw new \Exception("Not implemented.", 1700484664);
    }
}
