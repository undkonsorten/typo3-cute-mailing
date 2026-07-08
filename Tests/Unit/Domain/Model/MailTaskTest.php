<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Tests\Unit\Domain\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Undkonsorten\CuteMailing\Domain\Model\MailTask;
use Undkonsorten\Taskqueue\Domain\Model\Task;

#[CoversClass(MailTask::class)]
final class MailTaskTest extends UnitTestCase
{
    private MailTask $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new MailTask();
    }

    #[Test]
    public function isTask(): void
    {
        self::assertInstanceOf(Task::class, $this->subject);
    }

    #[Test]
    public function formatConstantsHaveExpectedValues(): void
    {
        self::assertSame('plain', MailTask::PLAINTEXT);
        self::assertSame('html', MailTask::HTML);
        self::assertSame('both', MailTask::BOTH);
    }

    #[Test]
    public function setAndGetFormatRoundTrips(): void
    {
        $this->subject->setFormat(MailTask::HTML);
        self::assertSame(MailTask::HTML, $this->subject->getFormat());
    }

    #[Test]
    public function newsletterInitiallyReturnsNull(): void
    {
        self::assertNull($this->subject->getNewsletter());
    }

    #[Test]
    public function setAndGetNewsletterRoundTrips(): void
    {
        $result = $this->subject->setNewsletter(5);
        self::assertSame($this->subject, $result);
        self::assertSame(5, $this->subject->getNewsletter());
    }

    #[Test]
    public function setAndGetRecipientRoundTrips(): void
    {
        $this->subject->setRecipient(7);
        self::assertSame(7, $this->subject->getRecipient());
    }

    #[Test]
    public function setAndGetSendOutRoundTrips(): void
    {
        $result = $this->subject->setSendOut(9);
        self::assertSame($this->subject, $result);
        self::assertSame(9, $this->subject->getSendOut());
    }

    #[Test]
    public function isAttachImagesInitiallyReturnsNull(): void
    {
        self::assertNull($this->subject->isAttachImages());
    }

    #[Test]
    public function setAndGetAttachImagesRoundTrips(): void
    {
        $result = $this->subject->setAttachImages(true);
        self::assertSame($this->subject, $result);
        self::assertTrue($this->subject->isAttachImages());
    }

    #[Test]
    public function getAdditionalDataReturnsNewsletterAndSendOut(): void
    {
        $this->subject->setNewsletter(5);
        $this->subject->setSendOut(9);

        self::assertSame(
            ['newsletter' => 5, 'sendOut' => 9],
            $this->subject->getAdditionalData(),
        );
    }
}
