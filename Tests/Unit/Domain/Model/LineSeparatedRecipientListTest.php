<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Tests\Unit\Domain\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use ReflectionProperty;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Undkonsorten\CuteMailing\Domain\Model\LineSeparatedRecipientList;
use Undkonsorten\CuteMailing\Domain\Model\RecipientList;
use Undkonsorten\CuteMailing\Domain\Model\SimpleRecipient;

#[CoversClass(LineSeparatedRecipientList::class)]
final class LineSeparatedRecipientListTest extends UnitTestCase
{
    private LineSeparatedRecipientList $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new LineSeparatedRecipientList();
    }

    #[Test]
    public function isRecipientList(): void
    {
        self::assertInstanceOf(RecipientList::class, $this->subject);
    }

    #[Test]
    public function getRecipientsReturnsEmptyArrayWhenListIsEmpty(): void
    {
        $this->setLineSeparatedList('');
        self::assertSame([], $this->subject->getRecipients());
    }

    #[Test]
    public function getRecipientsParsesOneAddressPerLine(): void
    {
        $this->setLineSeparatedList("peter@example.org\ngerd@example.org");

        $recipients = $this->subject->getRecipients();

        self::assertCount(2, $recipients);
        self::assertInstanceOf(SimpleRecipient::class, $recipients[0]);
        self::assertSame('peter@example.org', $recipients[0]->getEmail());
        self::assertSame('gerd@example.org', $recipients[1]->getEmail());
    }

    #[Test]
    public function getRecipientsTrimsWhitespaceAndIgnoresBlankLines(): void
    {
        $this->setLineSeparatedList("  peter@example.org  \n\n   \ngerd@example.org\n");

        $recipients = $this->subject->getRecipients();

        self::assertCount(2, $recipients);
        self::assertSame('peter@example.org', $recipients[0]->getEmail());
        self::assertSame('gerd@example.org', $recipients[1]->getEmail());
    }

    #[Test]
    public function getRecipientsAssignsPositionalUids(): void
    {
        $this->setLineSeparatedList("a@example.org\nb@example.org\nc@example.org");

        $recipients = $this->subject->getRecipients();

        self::assertSame(0, $recipients[0]->getUid());
        self::assertSame(1, $recipients[1]->getUid());
        self::assertSame(2, $recipients[2]->getUid());
    }

    #[Test]
    public function getRecipientsCountMatchesNumberOfParsedAddresses(): void
    {
        $this->setLineSeparatedList("a@example.org\nb@example.org\nc@example.org");
        self::assertSame(3, $this->subject->getRecipientsCount());
    }

    #[Test]
    public function getRecipientsCountIsZeroForEmptyList(): void
    {
        $this->setLineSeparatedList('');
        self::assertSame(0, $this->subject->getRecipientsCount());
    }

    #[Test]
    public function getRecipientReturnsMatchingRecipient(): void
    {
        $this->setLineSeparatedList("a@example.org\nb@example.org");

        $recipient = $this->subject->getRecipient(1);

        self::assertInstanceOf(SimpleRecipient::class, $recipient);
        self::assertSame('b@example.org', $recipient->getEmail());
    }

    #[Test]
    public function getRecipientReturnsNullWhenNotFound(): void
    {
        $this->setLineSeparatedList('a@example.org');
        self::assertNull($this->subject->getRecipient(99));
    }

    #[Test]
    public function removeRecipientByIdIsNotImplemented(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(1700484658);
        $this->subject->removeRecipientById(1);
    }

    #[Test]
    public function removeRecipientByEmailIsNotImplemented(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(1700484658);
        $this->subject->removeRecipientByEmail('a@example.org');
    }

    #[Test]
    public function disableRecipientByIdIsNotImplemented(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(1700484664);
        $this->subject->disableRecipientById(1);
    }

    #[Test]
    public function disableRecipientByEmailIsNotImplemented(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionCode(1700484664);
        $this->subject->disableRecipientByEmail('a@example.org');
    }

    private function setLineSeparatedList(string $value): void
    {
        $property = new ReflectionProperty($this->subject, 'lineSeparatedList');
        $property->setAccessible(true);
        $property->setValue($this->subject, $value);
    }
}
