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
    public function getRecipient(int $recipient): ?object
    {
        $filteredRecipients = array_filter($this->getRecipients(), function (SimpleRecipient $recipientObject) use ($recipient) {
            return $recipientObject->getUid() === $recipient;
        });
        return array_values($filteredRecipients)[0] ?? null;
    }
}
