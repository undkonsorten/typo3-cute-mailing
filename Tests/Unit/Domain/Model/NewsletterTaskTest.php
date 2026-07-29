<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Tests\Unit\Domain\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Undkonsorten\CuteMailing\Domain\Model\Newsletter;
use Undkonsorten\CuteMailing\Domain\Model\NewsletterTask;
use Undkonsorten\Taskqueue\Domain\Model\Task;

#[CoversClass(NewsletterTask::class)]
final class NewsletterTaskTest extends UnitTestCase
{
    private NewsletterTask $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new NewsletterTask();
    }

    #[Test]
    public function isTask(): void
    {
        self::assertInstanceOf(Task::class, $this->subject);
    }

    #[Test]
    public function getTestInitiallyReturnsFalse(): void
    {
        self::assertFalse($this->subject->getTest());
    }

    #[Test]
    public function setAndGetTestRoundTrips(): void
    {
        $this->subject->setTest(true);
        self::assertTrue($this->subject->getTest());
    }

    #[Test]
    public function getNewsletterInitiallyReturnsNull(): void
    {
        self::assertNull($this->subject->getNewsletter());
    }

    #[Test]
    public function setAndGetNewsletterRoundTrips(): void
    {
        $newsletter = new Newsletter();
        $result = $this->subject->setNewsletter($newsletter);

        self::assertSame($this->subject, $result);
        self::assertSame($newsletter, $this->subject->getNewsletter());
    }

    #[Test]
    public function isAttachImagesInitiallyReturnsNull(): void
    {
        self::assertNull($this->subject->isAttachImages());
    }

    #[Test]
    public function setAndGetAttachImagesRoundTrips(): void
    {
        $this->subject->setAttachImages(true);
        self::assertTrue($this->subject->isAttachImages());
    }

    #[Test]
    public function getAdditionalDataReturnsNewsletter(): void
    {
        $newsletter = new Newsletter();
        $this->subject->setNewsletter($newsletter);

        self::assertSame(['newsletter' => $newsletter], $this->subject->getAdditionalData());
    }
}
