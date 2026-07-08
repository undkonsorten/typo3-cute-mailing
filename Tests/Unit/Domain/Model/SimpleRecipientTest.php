<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Tests\Unit\Domain\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Undkonsorten\CuteMailing\Domain\Model\RecipientInterface;
use Undkonsorten\CuteMailing\Domain\Model\SimpleRecipient;

#[CoversClass(SimpleRecipient::class)]
final class SimpleRecipientTest extends UnitTestCase
{
    private SimpleRecipient $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new SimpleRecipient();
    }

    #[Test]
    public function isRecipientInterface(): void
    {
        self::assertInstanceOf(RecipientInterface::class, $this->subject);
    }

    #[Test]
    public function getUidInitiallyReturnsZero(): void
    {
        self::assertSame(0, $this->subject->getUid());
    }

    #[Test]
    public function setUidReturnsSelfAndSetsUid(): void
    {
        $result = $this->subject->setUid(42);
        self::assertSame($this->subject, $result);
        self::assertSame(42, $this->subject->getUid());
    }

    #[Test]
    public function getEmailInitiallyReturnsEmptyString(): void
    {
        self::assertSame('', $this->subject->getEmail());
    }

    #[Test]
    public function setEmailSetsEmail(): void
    {
        $this->subject->setEmail('peter@example.org');
        self::assertSame('peter@example.org', $this->subject->getEmail());
    }

    #[Test]
    public function getFirstNameInitiallyReturnsEmptyString(): void
    {
        self::assertSame('', $this->subject->getFirstName());
    }

    #[Test]
    public function setFirstNameSetsFirstName(): void
    {
        $this->subject->setFirstName('Peter');
        self::assertSame('Peter', $this->subject->getFirstName());
    }

    #[Test]
    public function getLastNameInitiallyReturnsEmptyString(): void
    {
        self::assertSame('', $this->subject->getLastName());
    }

    #[Test]
    public function setLastNameSetsLastName(): void
    {
        $this->subject->setLastName('Müller');
        self::assertSame('Müller', $this->subject->getLastName());
    }
}
