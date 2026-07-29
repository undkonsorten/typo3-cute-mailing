<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Tests\Functional\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Undkonsorten\CuteMailing\Command\SendMailCommand;
use Undkonsorten\CuteMailing\Domain\Model\NewsletterTask;
use Undkonsorten\Taskqueue\Domain\Repository\TaskRepository;

#[CoversClass(SendMailCommand::class)]
final class SendMailCommandTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'undkonsorten/taskqueue',
        'undkonsorten/typo3-cute-mailing',
    ];

    private TaskRepository $taskRepository;

    protected function setUp(): void
    {
        parent::setUp();
        // Extbase's ConfigurationManager needs a request to resolve query settings (e.g. storage
        // page respecting). Real CLI execution gets this from TYPO3's console bootstrap; CommandTester
        // bypasses that, so it must be set explicitly here.
        $request = (new ServerRequest())->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE);
        $this->get(ConfigurationManagerInterface::class)->setRequest($request);

        $this->taskRepository = $this->get(TaskRepository::class);
    }

    #[Test]
    public function queuesANewsletterTaskForTheGivenNewsletter(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Newsletter.csv');

        $tester = new CommandTester($this->get(SendMailCommand::class));
        $exitCode = $tester->execute(['newsletter' => 5]);

        self::assertSame(Command::SUCCESS, $exitCode);
        self::assertCount(1, $this->findNewsletterTasks());
    }

    #[Test]
    public function queuedNewsletterTaskReferencesTheCorrectNewsletter(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Newsletter.csv');

        $tester = new CommandTester($this->get(SendMailCommand::class));
        $tester->execute(['newsletter' => 5]);

        $newsletterTasks = $this->findNewsletterTasks();
        self::assertSame(5, $newsletterTasks[0]->getNewsletter()->getUid());
    }

    #[Test]
    public function failsWithoutQueueingWhenNewsletterDoesNotExist(): void
    {
        $tester = new CommandTester($this->get(SendMailCommand::class));
        $exitCode = $tester->execute(['newsletter' => 999]);

        self::assertSame(Command::FAILURE, $exitCode);
        self::assertCount(0, $this->findNewsletterTasks());
    }

    /**
     * @return NewsletterTask[]
     */
    private function findNewsletterTasks(): array
    {
        return array_values(array_filter(
            iterator_to_array($this->taskRepository->findAll()),
            static fn($task): bool => $task instanceof NewsletterTask,
        ));
    }
}
