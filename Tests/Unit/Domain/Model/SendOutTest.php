<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Tests\Unit\Domain\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Undkonsorten\CuteMailing\Domain\Model\MailTask;
use Undkonsorten\CuteMailing\Domain\Model\Newsletter;
use Undkonsorten\CuteMailing\Domain\Model\SendOut;
use Undkonsorten\Taskqueue\Domain\Model\TaskInterface;

#[CoversClass(SendOut::class)]
final class SendOutTest extends UnitTestCase
{
    private SendOut $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new SendOut();
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
    public function getMailTasksInitiallyReturnsEmptyObjectStorage(): void
    {
        self::assertCount(0, $this->subject->getMailTasks());
    }

    #[Test]
    public function addMailTaskAddsToMailTasks(): void
    {
        $mailTask = new MailTask();
        $result = $this->subject->addMailTask($mailTask);

        self::assertSame($this->subject, $result);
        self::assertCount(1, $this->subject->getMailTasks());
        self::assertTrue($this->subject->getMailTasks()->contains($mailTask));
    }

    #[Test]
    public function isTestInitiallyReturnsFalsyValue(): void
    {
        self::assertFalse((bool)$this->subject->isTest());
    }

    #[Test]
    public function setAndGetTestRoundTrips(): void
    {
        $result = $this->subject->setTest(true);
        self::assertSame($this->subject, $result);
        self::assertTrue($this->subject->isTest());
    }

    #[Test]
    public function totalInitiallyIsZero(): void
    {
        self::assertSame(0, $this->subject->getTotal());
    }

    #[Test]
    public function setTotalRoundTrips(): void
    {
        $result = $this->subject->setTotal(5);
        self::assertSame($this->subject, $result);
        self::assertSame(5, $this->subject->getTotal());
    }

    #[Test]
    public function completedInitiallyIsZero(): void
    {
        self::assertSame(0, $this->subject->getCompleted());
    }

    #[Test]
    public function setCompletedRoundTrips(): void
    {
        $result = $this->subject->setCompleted(3);
        self::assertSame($this->subject, $result);
        self::assertSame(3, $this->subject->getCompleted());
    }

    #[Test]
    public function incrementCompletedIncreasesCompletedByOne(): void
    {
        $this->subject->setCompleted(2);
        $result = $this->subject->incrementCompleted();

        self::assertSame($this->subject, $result);
        self::assertSame(3, $this->subject->getCompleted());
    }

    #[Test]
    public function getProgressReturnsZeroWhenTotalIsZero(): void
    {
        self::assertSame(0.0, $this->subject->getProgress());
    }

    #[Test]
    public function getProgressReturnsCompletedDividedByTotal(): void
    {
        $this->subject->setTotal(4);
        $this->subject->setCompleted(1);

        self::assertSame(0.25, $this->subject->getProgress());
    }

    #[Test]
    public function isCompleteIsFalseWhenProgressIsNotOne(): void
    {
        $this->subject->setTotal(4);
        $this->subject->setCompleted(1);

        self::assertFalse($this->subject->isComplete());
    }

    #[Test]
    public function isCompleteIsTrueWhenProgressIsOne(): void
    {
        $this->subject->setTotal(4);
        $this->subject->setCompleted(4);

        self::assertTrue($this->subject->isComplete());
    }

    #[Test]
    public function hasStartedIsFalseWhenNothingCompleted(): void
    {
        $this->subject->setTotal(4);
        self::assertFalse($this->subject->hasStarted());
    }

    #[Test]
    public function hasStartedIsTrueOnceSomethingCompleted(): void
    {
        $this->subject->setTotal(4);
        $this->subject->setCompleted(1);

        self::assertTrue($this->subject->hasStarted());
    }

    #[Test]
    public function isScheduledIsTrueWhenNothingCompleted(): void
    {
        $this->subject->setTotal(4);
        self::assertTrue($this->subject->isScheduled());
    }

    #[Test]
    public function isScheduledIsFalseOnceSomethingCompleted(): void
    {
        $this->subject->setTotal(4);
        $this->subject->setCompleted(1);

        self::assertFalse($this->subject->isScheduled());
    }

    #[Test]
    public function isRunningIsTrueWhenStartedButNotComplete(): void
    {
        $this->subject->setTotal(4);
        $this->subject->setCompleted(1);

        self::assertTrue($this->subject->isRunning());
    }

    #[Test]
    public function isRunningIsFalseWhenNotStarted(): void
    {
        $this->subject->setTotal(4);
        self::assertFalse($this->subject->isRunning());
    }

    #[Test]
    public function isRunningIsFalseWhenComplete(): void
    {
        $this->subject->setTotal(4);
        $this->subject->setCompleted(4);

        self::assertFalse($this->subject->isRunning());
    }

    #[Test]
    public function getCompletedMailTasksReturnsOnlyFinishedAndFailedTasks(): void
    {
        $waiting = new MailTask();
        $waiting->setStatus(TaskInterface::WAITING);

        $finished = new MailTask();
        $finished->setStatus(TaskInterface::FINISHED);

        $failed = new MailTask();
        $failed->setStatus(TaskInterface::FAILED);

        $running = new MailTask();
        $running->setStatus(TaskInterface::RUNNING);

        $this->subject->addMailTask($waiting);
        $this->subject->addMailTask($finished);
        $this->subject->addMailTask($failed);
        $this->subject->addMailTask($running);

        $completed = $this->subject->getCompletedMailTasks();

        self::assertCount(2, $completed);
        self::assertContains($finished, $completed);
        self::assertContains($failed, $completed);
    }

    #[Test]
    public function getCompletedMailTasksReturnsEmptyArrayWhenNoneCompleted(): void
    {
        $waiting = new MailTask();
        $waiting->setStatus(TaskInterface::WAITING);
        $this->subject->addMailTask($waiting);

        self::assertSame([], $this->subject->getCompletedMailTasks());
    }
}
