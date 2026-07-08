<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Tests\Functional\Services;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use ReflectionProperty;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Undkonsorten\CuteMailing\Domain\Model\LineSeparatedRecipientList;
use Undkonsorten\CuteMailing\Domain\Model\MailTask;
use Undkonsorten\CuteMailing\Domain\Model\Newsletter;
use Undkonsorten\CuteMailing\Domain\Model\NewsletterTask;
use Undkonsorten\CuteMailing\Domain\Repository\NewsletterRepository;
use Undkonsorten\CuteMailing\Domain\Repository\SendOutRepository;
use Undkonsorten\CuteMailing\Services\NewsletterService;
use Undkonsorten\Taskqueue\Domain\Repository\TaskRepository;

#[CoversClass(NewsletterService::class)]
final class NewsletterServiceTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'undkonsorten/taskqueue',
        'undkonsorten/typo3-cute-mailing',
    ];

    private NewsletterService $subject;
    private NewsletterRepository $newsletterRepository;
    private SendOutRepository $sendOutRepository;
    private TaskRepository $taskRepository;
    private PersistenceManagerInterface $persistenceManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->get(NewsletterService::class);
        $this->newsletterRepository = $this->get(NewsletterRepository::class);
        $this->sendOutRepository = $this->get(SendOutRepository::class);
        $this->taskRepository = $this->get(TaskRepository::class);
        $this->persistenceManager = $this->get(PersistenceManagerInterface::class);
    }

    #[Test]
    public function runNewsletterThrowsWhenTaskHasNoNewsletter(): void
    {
        $task = new NewsletterTask();

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(1643821994);

        $this->subject->runNewsletter($task);
    }

    #[Test]
    public function runNewsletterThrowsWhenRecipientListIsEmpty(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/NewsletterWithRecipientLists.csv');
        $newsletter = $this->fetchNewsletterWithRecipients('', 'test@example.org');

        $task = new NewsletterTask();
        $task->setNewsletter($newsletter);
        $task->setTest(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionCode(1644851884);

        $this->subject->runNewsletter($task);
    }

    #[Test]
    public function runNewsletterQueuesOneMailTaskPerRealRecipient(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/NewsletterWithRecipientLists.csv');
        $newsletter = $this->fetchNewsletterWithRecipients(
            "a@example.org\nb@example.org\nc@example.org",
            'test@example.org',
        );

        $task = new NewsletterTask();
        $task->setNewsletter($newsletter);
        $task->setTest(false);

        $this->subject->runNewsletter($task);
        $this->persistenceManager->persistAll();

        $mailTasks = $this->findMailTasks();
        self::assertCount(3, $mailTasks);
        foreach ($mailTasks as $mailTask) {
            self::assertSame($newsletter->getUid(), $mailTask->getNewsletter());
            self::assertSame(MailTask::BOTH, $mailTask->getFormat());
            self::assertFalse($mailTask->isAttachImages());
        }
    }

    #[Test]
    public function runNewsletterCreatesASendOutWithMatchingTotal(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/NewsletterWithRecipientLists.csv');
        $newsletter = $this->fetchNewsletterWithRecipients(
            "a@example.org\nb@example.org",
            'test@example.org',
        );

        $task = new NewsletterTask();
        $task->setNewsletter($newsletter);
        $task->setTest(false);

        $this->subject->runNewsletter($task);
        $this->persistenceManager->persistAll();

        $sendOuts = $this->sendOutRepository->findAll();
        self::assertCount(1, $sendOuts);
        $sendOut = $sendOuts->getFirst();
        self::assertSame(2, $sendOut->getTotal());
        self::assertFalse($sendOut->isTest());
    }

    #[Test]
    public function runNewsletterUsesTestRecipientListForTestRuns(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/NewsletterWithRecipientLists.csv');
        $newsletter = $this->fetchNewsletterWithRecipients(
            "a@example.org\nb@example.org\nc@example.org",
            'test@example.org',
        );

        $task = new NewsletterTask();
        $task->setNewsletter($newsletter);
        $task->setTest(true);
        $task->setAttachImages(true);

        $this->subject->runNewsletter($task);
        $this->persistenceManager->persistAll();

        $mailTasks = $this->findMailTasks();
        self::assertCount(1, $mailTasks);
        self::assertTrue($mailTasks[0]->isAttachImages());

        $sendOuts = $this->sendOutRepository->findAll();
        self::assertTrue($sendOuts->getFirst()->isTest());
    }

    /**
     * @return MailTask[]
     */
    private function findMailTasks(): array
    {
        return array_values(array_filter(
            iterator_to_array($this->taskRepository->findAll()),
            static fn($task): bool => $task instanceof MailTask,
        ));
    }

    private function fetchNewsletterWithRecipients(string $realRecipients, string $testRecipients): Newsletter
    {
        $newsletter = $this->newsletterRepository->findByUid(1);
        self::assertInstanceOf(Newsletter::class, $newsletter);

        $recipientList = $newsletter->getRecipientList();
        self::assertInstanceOf(LineSeparatedRecipientList::class, $recipientList);
        $this->setLineSeparatedList($recipientList, $realRecipients);

        $testRecipientList = $newsletter->getTestRecipientList();
        self::assertInstanceOf(LineSeparatedRecipientList::class, $testRecipientList);
        $this->setLineSeparatedList($testRecipientList, $testRecipients);

        return $newsletter;
    }

    private function setLineSeparatedList(LineSeparatedRecipientList $list, string $value): void
    {
        $property = new ReflectionProperty($list, 'lineSeparatedList');
        $property->setAccessible(true);
        $property->setValue($list, $value);
    }
}
