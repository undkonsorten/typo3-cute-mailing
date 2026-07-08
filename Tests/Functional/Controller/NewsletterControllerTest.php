<?php

declare(strict_types=1);

namespace Undkonsorten\CuteMailing\Tests\Functional\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Routing\Route;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use Undkonsorten\CuteMailing\Controller\NewsletterController;
use Undkonsorten\CuteMailing\Domain\Model\Newsletter;
use Undkonsorten\CuteMailing\Domain\Model\NewsletterTask;
use Undkonsorten\CuteMailing\Domain\Repository\NewsletterRepository;
use Undkonsorten\Taskqueue\Domain\Repository\TaskRepository;

#[CoversClass(NewsletterController::class)]
final class NewsletterControllerTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'undkonsorten/taskqueue',
        'undkonsorten/typo3-cute-mailing',
    ];

    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/cute_mailing/Tests/Functional/Controller/Fixtures/Sites/' => 'config/sites',
    ];

    private const MODULE_PATH = '/module/page/cute-mailing';

    private NewsletterRepository $newsletterRepository;
    private TaskRepository $taskRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->importCSVDataSet(__DIR__ . '/Fixtures/Database/BackendUser.csv');
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Database/Pages.csv');
        $this->setUpBackendUser(1);
        $GLOBALS['LANG'] = $this->get(LanguageServiceFactory::class)->create('en');

        $this->newsletterRepository = $this->get(NewsletterRepository::class);
        $this->taskRepository = $this->get(TaskRepository::class);
    }

    #[Test]
    public function listActionRendersNewslettersOnCurrentPage(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Database/TwoNewsletters.csv');

        $html = (string)$this->executeRequest('list', ['id' => 1])->getBody();

        self::assertStringContainsString('Newsletter One', $html);
        self::assertStringContainsString('Newsletter Two', $html);
    }

    #[Test]
    public function listActionForwardsToChoosePageWhenNoPageIdIsGiven(): void
    {
        $response = $this->executeRequest('list', []);

        self::assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function choosePageActionRendersSuccessfully(): void
    {
        $response = $this->executeRequest('choosePage', []);

        self::assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function prepareActionRendersSuccessfully(): void
    {
        $response = $this->executeRequest('prepare', ['id' => 1]);

        self::assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function newActionRendersSuccessfullyForACurrentPageWithConfiguredPageTypes(): void
    {
        $response = $this->executeRequest('new', ['id' => 1]);

        self::assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function editActionRendersExistingNewsletter(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Database/OneCreatedNewsletter.csv');

        $html = (string)$this->executeRequest('edit', ['id' => 1], ['newsletter' => 5])->getBody();

        self::assertStringContainsString('My Newsletter', $html);
    }

    #[Test]
    public function createActionPersistsNewNewsletterUnderTheCuteMailingFolderAndRedirects(): void
    {
        $response = $this->executeRequest('create', ['id' => 1], [
            'newsletter' => [
                'title' => 'Brand New Newsletter',
                'subject' => 'Hello',
                'sender' => 'sender@example.org',
                'senderName' => 'Sender',
                'replyTo' => 'reply@example.org',
                'replyToName' => 'Reply',
                'newsletterPage' => '1',
            ],
        ]);

        self::assertSame(303, $response->getStatusCode());

        $newsletters = $this->newsletterRepository->findAll();
        self::assertCount(1, $newsletters);
        self::assertSame('Brand New Newsletter', $newsletters->getFirst()->getTitle());
        self::assertSame(1, $newsletters->getFirst()->getPid());
    }

    #[Test]
    public function updateActionPersistsChangesAndRedirects(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Database/OneCreatedNewsletter.csv');

        $response = $this->executeRequest('update', ['id' => 1], [
            'newsletter' => [
                '__identity' => '5',
                'title' => 'Updated title',
                'subject' => 'Hello',
                'sender' => 'sender@example.org',
                'senderName' => 'Sender',
                'replyTo' => 'reply@example.org',
                'replyToName' => 'Reply',
                'newsletterPage' => '1',
            ],
        ]);

        self::assertSame(303, $response->getStatusCode());
        self::assertSame('Updated title', $this->newsletterRepository->findByUid(5)->getTitle());
    }

    #[Test]
    public function deleteActionRemovesTheNewsletter(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Database/OneCreatedNewsletter.csv');

        $response = $this->executeRequest('delete', ['id' => 1], ['newsletter' => 5]);

        self::assertSame(303, $response->getStatusCode());
        self::assertNull($this->newsletterRepository->findByUid(5));
    }

    #[Test]
    public function enableActionQueuesANewsletterTaskAndSchedulesTheNewsletter(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Database/OneCreatedNewsletter.csv');

        $response = $this->executeRequest('enable', ['id' => 1], ['newsletter' => 5]);

        self::assertSame(303, $response->getStatusCode());
        self::assertSame(Newsletter::SCHEDULED, $this->newsletterRepository->findByUid(5)->getStatus());
        self::assertCount(1, $this->findQueuedNewsletterTasks());
    }

    #[Test]
    public function enableActionDoesNotRequeueAnAlreadyScheduledNewsletter(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Database/OneScheduledNewsletter.csv');

        $this->executeRequest('enable', ['id' => 1], ['newsletter' => 5]);

        self::assertCount(0, $this->findQueuedNewsletterTasks());
    }

    #[Test]
    public function sendTestMailActionQueuesATestNewsletterTaskWhenATestRecipientListIsConfigured(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Database/OneCreatedNewsletterWithTestRecipientList.csv');

        $response = $this->executeRequest('sendTestMail', ['id' => 1], ['newsletter' => 5]);

        self::assertSame(303, $response->getStatusCode());
        self::assertSame(Newsletter::TESTED, $this->newsletterRepository->findByUid(5)->getStatus());

        $tasks = $this->findQueuedNewsletterTasks();
        self::assertCount(1, $tasks);
        self::assertTrue($tasks[0]->getTest());
    }

    #[Test]
    public function sendTestMailActionDoesNotQueueAnythingWithoutATestRecipientList(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Database/OneCreatedNewsletter.csv');

        $this->executeRequest('sendTestMail', ['id' => 1], ['newsletter' => 5]);

        self::assertCount(0, $this->findQueuedNewsletterTasks());
    }

    #[Test]
    public function wizardUserPreviewAjaxReturnsRecipientPreviewAsJson(): void
    {
        $this->importCSVDataSet(__DIR__ . '/Fixtures/Database/OneCreatedNewsletterWithTestRecipientList.csv');

        $request = (new ServerRequest(self::MODULE_PATH . '/wizardUserPreview'))
            ->withQueryParams(['recipientList' => 1]);

        $response = $this->get(NewsletterController::class)->wizardUserPreviewAjax($request);
        $payload = json_decode((string)$response->getBody(), true);

        self::assertIsArray($payload);
        self::assertArrayHasKey('html', $payload);
        self::assertStringContainsString('test@example.org', $payload['html']);
    }

    /**
     * @return NewsletterTask[]
     */
    private function findQueuedNewsletterTasks(): array
    {
        return array_values(array_filter(
            iterator_to_array($this->taskRepository->findAll()),
            static fn($task): bool => $task instanceof NewsletterTask,
        ));
    }

    private function executeRequest(string $action, array $queryParams, array $extbaseArguments = []): ResponseInterface
    {
        $request = $this->createRequest($action, $queryParams, $extbaseArguments);
        $GLOBALS['TYPO3_REQUEST'] = $request;

        $isCli = Environment::isCli();
        Environment::initialize(
            Environment::getContext(),
            false,
            Environment::isComposerMode(),
            Environment::getProjectPath(),
            Environment::getPublicPath(),
            Environment::getVarPath(),
            Environment::getConfigPath(),
            Environment::getCurrentScript(),
            Environment::toArray()['os'],
        );

        $response = $this->get(NewsletterController::class)->processRequest(new Request($request));

        Environment::initialize(
            Environment::getContext(),
            $isCli,
            Environment::isComposerMode(),
            Environment::getProjectPath(),
            Environment::getPublicPath(),
            Environment::getVarPath(),
            Environment::getConfigPath(),
            Environment::getCurrentScript(),
            Environment::toArray()['os'],
        );

        return $response;
    }

    private function createRequest(string $action, array $queryParams, array $extbaseArguments): ServerRequest
    {
        $route = self::MODULE_PATH . '/NewsletterController/' . $action;

        $extbaseParameters = (new ExtbaseRequestParameters(NewsletterController::class))
            ->setControllerObjectName(NewsletterController::class)
            ->setControllerActionName($action)
            ->setPluginName('cutemailing')
            ->setArguments($extbaseArguments);

        $normalizedParams = self::createStub(NormalizedParams::class);
        $normalizedParams->method('getRequestUri')->willReturn('typo3' . $route);

        $site = $this->get(SiteFinder::class)->getSiteByRootPageId(1);

        return (new ServerRequest($route))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
            ->withAttribute('site', $site)
            ->withAttribute('route', new Route($route, ['packageName' => 'undkonsorten/typo3-cute-mailing']))
            ->withAttribute('normalizedParams', $normalizedParams)
            ->withAttribute('extbase', $extbaseParameters)
            ->withQueryParams($queryParams);
    }
}
